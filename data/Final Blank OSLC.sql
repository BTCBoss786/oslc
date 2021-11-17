-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2021 at 08:09 AM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 8.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `oslc`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `DailyWagesCalculator` (IN `aid` INT)  BEGIN
	SELECT SUM(`WorkingAmt`), SUM(`OvertimeAmt`) INTO @workAmt, @otAmt FROM `AttendanceList` GROUP BY `AttendanceId` HAVING `AttendanceId` = aid;
    SELECT `Commission` INTO @commission FROM `Companies` WHERE `CompanyId` = (SELECT `CompanyId` FROM `Attendance` WHERE `AttendanceId` = aid LIMIT 1);
    SET @commissionAmt := (((@workAmt + @otAmt) * @commission) / 100);
    UPDATE `Attendance` 
    SET `TotalWorking` = @workAmt, 
        `TotalOvertime` = @otAmt, 
        `CommissionAmt` = @commissionAmt 
    WHERE `AttendanceId` = aid;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetLaboursPay` (IN `startDate` DATE, IN `endDate` DATE)  BEGIN
	SET @exists = (SELECT EXISTS (SELECT * FROM `Salaries` WHERE `SalaryFrom` = startDate AND `SalaryTo` = endDate));
    IF @exists = 1 THEN
        SELECT S.*, L.`LabourName` FROM `Salaries` S 
        INNER JOIN `Labours` L ON S.`LabourId` = L.`LabourId`
        WHERE `SalaryFrom` = startDate AND `SalaryTo` = endDate;
    ELSE
        SELECT L.`LabourId`,
            L.`LabourName`,
            SUM(IF(AL.`WorkingHrs` = 8, 1, 0)) AS `WorkDay`,
            SUM(IF(AL.`WorkingHrs` = 4, 1, 0)) AS `HalfDay`,
            SUM(AL.`OvertimeHrs`) AS `OTHrs`,
            SUM(AL.`WorkingAmt`) AS `BasicPay`,
            SUM(AL.`OvertimeAmt`) AS `Overtime`,
            L.`Advance`
        FROM `Labours` L
        LEFT JOIN `AttendanceList` AL ON L.`LabourId` = AL.`LabourId`
        INNER JOIN `Attendance` A ON AL.`AttendanceId` = A.`AttendanceId`
        WHERE A.`AttendanceDate` BETWEEN startDate AND endDate
        GROUP BY L.`LabourId`;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `LabourWagesCalculator` (IN `aid` INT, IN `lid` INT, IN `WorkingHrs` INT, IN `OvertimeHrs` INT, OUT `WorkingAmt` DECIMAL, OUT `OvertimeAmt` DECIMAL)  BEGIN
    SELECT `Skilled` INTO @isSkilled FROM `Labours` WHERE `LabourId` = lid;
    SELECT `CompanyId`, `AttendanceDate` INTO @cid, @aDate FROM `Attendance` WHERE `AttendanceId` = aid;
    SELECT ((`BasicPay`+`DA`) / 8) INTO @skillPay FROM `CompanyPay` WHERE `CompanyId` = @cid AND `Category` = 'Skilled' AND `EffectiveDate` <= @aDate ORDER BY `EffectiveDate` DESC LIMIT 1;
    SELECT ((`BasicPay`+`DA`) / 8) INTO @unskillPay FROM `CompanyPay` WHERE `CompanyId` = @cid AND `Category` = 'Un-Skilled' AND `EffectiveDate` <= @aDate ORDER BY `EffectiveDate` DESC LIMIT 1;

    IF @isSkilled = 1 THEN
        SET WorkingAmt := (WorkingHrs * @skillPay);
        SET OvertimeAmt := (OvertimeHrs * @skillPay);
    ELSE
        SET WorkingAmt := (WorkingHrs * @unskillPay);
        SET OvertimeAmt := (OvertimeHrs * @unskillPay);
    END IF;
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `GetAttendanceId` (`aDate` DATE, `cId` INT) RETURNS INT(11) BEGIN
    DECLARE aId INT;
    IF EXISTS(SELECT `AttendanceId` FROM `Attendance` WHERE `AttendanceDate` = aDate AND `CompanyId` = cId) THEN
        SELECT `AttendanceId` INTO aId FROM `Attendance` WHERE `AttendanceDate` = aDate AND `CompanyId` = cId;
    ELSE
    	INSERT INTO `Attendance`(`AttendanceDate`, `CompanyId`) VALUES(aDate, cId);
        SELECT LAST_INSERT_ID() INTO aId;
    END IF;
    RETURN (aId);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `AttendanceId` int(11) NOT NULL,
  `AttendanceDate` date NOT NULL,
  `CompanyId` int(11) NOT NULL,
  `TotalPresent` int(10) UNSIGNED DEFAULT 0,
  `TotalWorking` decimal(10,2) UNSIGNED DEFAULT 0.00,
  `TotalOvertime` decimal(10,2) UNSIGNED DEFAULT 0.00,
  `CommissionAmt` decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendancelist`
--

CREATE TABLE `attendancelist` (
  `AttendanceListId` int(11) NOT NULL,
  `AttendanceId` int(11) NOT NULL,
  `LabourId` int(11) NOT NULL,
  `WorkingHrs` int(10) UNSIGNED DEFAULT NULL,
  `OvertimeHrs` int(10) UNSIGNED DEFAULT NULL,
  `WorkingAmt` decimal(10,2) DEFAULT NULL,
  `OvertimeAmt` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `attendancelist`
--
DELIMITER $$
CREATE TRIGGER `after_delete_attendancelist` AFTER DELETE ON `attendancelist` FOR EACH ROW BEGIN
  	UPDATE Attendance SET TotalPresent = TotalPresent - 1 WHERE AttendanceId = OLD.AttendanceId;
    CALL DailyWagesCalculator(OLD.AttendanceId);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_insert_attendancelist` AFTER INSERT ON `attendancelist` FOR EACH ROW BEGIN
	CALL DailyWagesCalculator(NEW.AttendanceId);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_attendancelist` AFTER UPDATE ON `attendancelist` FOR EACH ROW BEGIN
	CALL DailyWagesCalculator(OLD.AttendanceId);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_insert_attendancelist` BEFORE INSERT ON `attendancelist` FOR EACH ROW BEGIN
	CALL LabourWagesCalculator(NEW.AttendanceId, NEW.LabourId, NEW.WorkingHrs, NEW.OvertimeHrs, @workingAmt, @overtimeAmt);
	SET NEW.WorkingAmt = @workingAmt;
	SET NEW.OvertimeAmt = @overtimeAmt;
  	UPDATE Attendance SET TotalPresent = TotalPresent + 1 WHERE AttendanceId = NEW.AttendanceId;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_update_attendancelist` BEFORE UPDATE ON `attendancelist` FOR EACH ROW BEGIN
	CALL LabourWagesCalculator(OLD.AttendanceId, OLD.LabourId, NEW.WorkingHrs, NEW.OvertimeHrs, @workingAmt, @overtimeAmt);
	SET NEW.WorkingAmt = @workingAmt;
	SET NEW.OvertimeAmt = @overtimeAmt;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `CompanyId` int(10) UNSIGNED NOT NULL,
  `CompanyName` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `GSTIN` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ContactPerson` varchar(48) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Designation` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `MobileNo` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Commission` decimal(5,2) NOT NULL,
  `JoinDate` datetime NOT NULL DEFAULT current_timestamp(),
  `Status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companypay`
--

CREATE TABLE `companypay` (
  `CompanyPayId` int(11) NOT NULL,
  `CompanyId` int(11) NOT NULL,
  `Category` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `BasicPay` decimal(10,2) NOT NULL,
  `DA` decimal(10,2) NOT NULL,
  `EffectiveDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `GroupId` int(10) UNSIGNED NOT NULL,
  `Name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Permission` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`GroupId`, `Name`, `Permission`) VALUES
(1, 'Administrator', '{\"admin\": 1}'),
(2, 'Standard', '{\"user\": 1}');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `InvoiceId` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Reference` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Total` double(10,2) NOT NULL,
  `Bonus` double(10,2) NOT NULL,
  `Commission` double(10,2) NOT NULL,
  `Tax` double(10,2) NOT NULL,
  `EPF` double(10,2) NOT NULL,
  `Amount` double(10,2) NOT NULL,
  `Received` tinyint(4) NOT NULL DEFAULT 0,
  `CompanyId` int(11) NOT NULL,
  `Month` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `invoices`
--
DELIMITER $$
CREATE TRIGGER `before_insert_invoices` BEFORE INSERT ON `invoices` FOR EACH ROW BEGIN
	SET @total := NEW.Total;
	SET @bonus := NEW.Bonus;
	SET @comm := NEW.Commission;
	SET @epf := NEW.EPF;
    SET @tax := (@total + @bonus + @comm) * 0.18;
    SET NEW.`Tax` = @tax;
    SET NEW.`Amount` = @total + @bonus + @comm + @epf + @tax;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_update_invoices` BEFORE UPDATE ON `invoices` FOR EACH ROW BEGIN
	SET @total := NEW.Total;
	SET @bonus := NEW.Bonus;
	SET @comm := NEW.Commission;
	SET @epf := NEW.EPF;
    SET @tax := (@total + @bonus + @comm) * 0.18;
    SET NEW.`Tax` = @tax;
    SET NEW.`Amount` = @total + @bonus + @comm + @epf + @tax;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `labours`
--

CREATE TABLE `labours` (
  `LabourId` int(11) NOT NULL,
  `LabourName` varchar(48) COLLATE utf8mb4_unicode_ci NOT NULL,
  `BirthDate` date NOT NULL,
  `Gender` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Education` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Married` int(11) NOT NULL,
  `MobileNo` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `Skilled` int(11) NOT NULL DEFAULT 0,
  `AadhaarNo` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PANNo` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PFNo` varchar(48) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Advance` decimal(10,0) NOT NULL,
  `Arrear` decimal(10,0) NOT NULL,
  `BankName` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `IFSCCode` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `AccountNo` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Branch` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `RelName` varchar(48) COLLATE utf8mb4_unicode_ci NOT NULL,
  `RelType` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `RelMobile` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `RelAddress` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `JoinDate` datetime NOT NULL DEFAULT current_timestamp(),
  `Status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `PaymentId` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Type` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Mode` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Amount` decimal(10,0) NOT NULL,
  `Description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `CreationDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `payments`
--
DELIMITER $$
CREATE TRIGGER `after_delete_payments` AFTER DELETE ON `payments` FOR EACH ROW BEGIN
	SET @type := OLD.Type;
	SET @description := OLD.Description;
    IF @type = "Advance" THEN
    	UPDATE `Labours`
        SET `Advance` = `Advance` - OLD.Amount
        WHERE `LabourId` = @description;
    END IF;
    IF @type = "Salary" THEN
    	UPDATE `Salaries`
        SET `Paid` = 0
        WHERE SUBSTRING(`SalaryFrom`, 1, 7) = @description AND SUBSTRING(`SalaryTo`, 1, 7) = @description;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_insert_payments` AFTER INSERT ON `payments` FOR EACH ROW BEGIN
	SET @type := NEW.Type;
	SET @description := NEW.Description;
    IF @type = "Advance" THEN
    	UPDATE `Labours`
        SET `Advance` = `Advance` + NEW.Amount
        WHERE `LabourId` = @description;
    END IF;
    IF @type = "Salary" THEN
    	UPDATE `Salaries`
        SET `Paid` = 1
        WHERE SUBSTRING(`SalaryFrom`, 1, 7) = @description AND SUBSTRING(`SalaryTo`, 1, 7) = @description;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `ReceiptId` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Type` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Mode` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Amount` decimal(10,0) NOT NULL,
  `Description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `CreationDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `receipts`
--
DELIMITER $$
CREATE TRIGGER `after_delete_receipts` AFTER DELETE ON `receipts` FOR EACH ROW BEGIN
	SET @type := OLD.Type;
	SET @description := OLD.Description;
    IF @type = "Invoice" THEN
    	UPDATE `Invoices`
        SET `Received` = 0
        WHERE `InvoiceId` = @description;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_insert_receipts` AFTER INSERT ON `receipts` FOR EACH ROW BEGIN
	SET @type := NEW.Type;
	SET @description := NEW.Description;
    IF @type = "Invoice" THEN
    	UPDATE `Invoices`
        SET `Received` = 1
        WHERE `InvoiceId` = @description;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `salaries`
--

CREATE TABLE `salaries` (
  `SalaryId` int(11) NOT NULL,
  `SalaryFrom` date NOT NULL,
  `SalaryTo` date NOT NULL,
  `LabourId` int(11) NOT NULL,
  `BasicPay` double(10,2) NOT NULL,
  `Overtime` double(10,2) NOT NULL,
  `Allowances` double(10,2) NOT NULL,
  `Bonus` double(10,2) NOT NULL,
  `GrossPay` double(10,2) NOT NULL,
  `Advance` decimal(10,0) NOT NULL,
  `ProvidentFund` double(10,2) NOT NULL,
  `ProfessionalTax` double(10,2) NOT NULL,
  `Deductions` double(10,2) NOT NULL,
  `NetSalary` double(10,2) NOT NULL,
  `Paid` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `salaries`
--
DELIMITER $$
CREATE TRIGGER `after_insert_salaries` AFTER INSERT ON `salaries` FOR EACH ROW BEGIN
	UPDATE `Labours` SET `Advance` = Advance - NEW.Advance WHERE `LabourId` = NEW.LabourId;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserId` int(10) UNSIGNED NOT NULL,
  `Username` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Password` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Salt` varchar(96) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Secret` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `GroupId` int(11) NOT NULL,
  `FullName` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `JoinDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserId`, `Username`, `Password`, `Salt`, `Secret`, `GroupId`, `FullName`, `JoinDate`) VALUES
(1, 'Admin', 'e78d41567bf602b53b9e6d7a11d7f9f4862e642b09047a4f100150c1ae393c6a', '539e140a93f9f9177ae68df6f0f509c6951c62cd734b4b48104f887961c840f2', '9999', 1, 'Adminstrator', '2021-10-12 11:38:48');

-- --------------------------------------------------------

--
-- Table structure for table `users_session`
--

CREATE TABLE `users_session` (
  `SessionId` int(10) UNSIGNED NOT NULL,
  `UserId` int(10) UNSIGNED NOT NULL,
  `Hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`AttendanceId`),
  ADD UNIQUE KEY `AttendanceDate` (`AttendanceDate`,`CompanyId`);

--
-- Indexes for table `attendancelist`
--
ALTER TABLE `attendancelist`
  ADD PRIMARY KEY (`AttendanceListId`),
  ADD UNIQUE KEY `AttendanceId` (`AttendanceId`,`LabourId`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`CompanyId`),
  ADD UNIQUE KEY `GSTIN` (`GSTIN`);

--
-- Indexes for table `companypay`
--
ALTER TABLE `companypay`
  ADD PRIMARY KEY (`CompanyPayId`),
  ADD UNIQUE KEY `CompanyId` (`CompanyId`,`Category`,`EffectiveDate`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`GroupId`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`InvoiceId`),
  ADD UNIQUE KEY `Reference` (`Reference`);

--
-- Indexes for table `labours`
--
ALTER TABLE `labours`
  ADD PRIMARY KEY (`LabourId`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`PaymentId`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`ReceiptId`);

--
-- Indexes for table `salaries`
--
ALTER TABLE `salaries`
  ADD PRIMARY KEY (`SalaryId`),
  ADD UNIQUE KEY `SalaryFrom` (`SalaryFrom`,`SalaryTo`,`LabourId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserId`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indexes for table `users_session`
--
ALTER TABLE `users_session`
  ADD PRIMARY KEY (`SessionId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `AttendanceId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendancelist`
--
ALTER TABLE `attendancelist`
  MODIFY `AttendanceListId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `CompanyId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companypay`
--
ALTER TABLE `companypay`
  MODIFY `CompanyPayId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `GroupId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `InvoiceId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `labours`
--
ALTER TABLE `labours`
  MODIFY `LabourId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `PaymentId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `ReceiptId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salaries`
--
ALTER TABLE `salaries`
  MODIFY `SalaryId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users_session`
--
ALTER TABLE `users_session`
  MODIFY `SessionId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendancelist`
--
ALTER TABLE `attendancelist`
  ADD CONSTRAINT `attendancelist_ibfk_1` FOREIGN KEY (`AttendanceId`) REFERENCES `attendance` (`AttendanceId`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
