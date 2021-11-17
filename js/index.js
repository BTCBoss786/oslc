$(function () {
    let signInBtn = $("a[href='#signInDiv']");
    let forgotPasswordBtn = $("a[href='#resetPasswordDiv']");

    forgotPasswordBtn.unbind("click").bind("click", function () {
        $("#signInDiv").fadeOut("slow").addClass("d-none");
        $("#resetPasswordDiv").fadeIn("slow").removeClass("d-none");
    });

    signInBtn.unbind("click").bind("click", function () {
        $("#resetPasswordDiv").fadeOut("slow").addClass("d-none");
        $("#signInDiv").fadeIn("slow").removeClass("d-none");
    });

    if (window.location.hash === "#resetPasswordDiv") {
        forgotPasswordBtn.click();
    }

    // let loginForm = $("button[name='signIn']").closest("form");
    // let forgotPasswordForm = $("button[name='resetPassword']").closest("form");
    // let tokenFields = $("input[name='token']");
    // tokenFields.each(function () {
    //     let token = newToken();
    //     $(this).val(token);
    // });
    // loginForm.unbind("submit").bind("submit", function (e) {
    //     e.preventDefault();
    //     $("button[name='signIn']").addClass("disabled");
    //     $("button[name='signIn']").children("div").removeClass("d-none");
    //     $.ajax({
    //         url: "./php/authProcess.php",
    //         type: "post",
    //         data: $(this).serialize() + "&signIn=signIn",
    //         dataType: "json"
    //     }).done(function (response) {
    //         $("button[name='signIn']").removeClass("disabled");
    //         $("button[name='signIn']").children("div").addClass("d-none");
    //         if (response.status === true) {
    //             $("#response").html("");
    //         } else {
    //             $("#response").html('<div class="alert bg-transparent fade show small text-danger">' + response.data.join("<br />") + '</div>');
    //         }
    //     });
    // });
});