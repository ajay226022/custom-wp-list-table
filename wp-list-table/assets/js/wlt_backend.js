jQuery(document).ready(function () {
    // alert ("hi")
    jQuery(document).on('click', '#update', function () {
        let id = jQuery(this).attr("data-id");
        let name = jQuery("#name").val();
        let email = jQuery("#email").val();
        let number = jQuery("#number").val();
        // let img = jQuery("#img").val();
        let date = jQuery('#input_date').val();
        alert(date);
        console.log(date);

        jQuery.ajax({

        //     url: "admin-ajax.php",
        //     type: "POST",
        //     dataType: "JSON",
        //     data:
        //     {
        //         'id': id,
        //         'name': name,
        //         'email': email,
        //         'number': number,
        //         'date': date
        //     },
        //     success: function (response) {
        //         if (response.status == "success") {
        //             jQuery("#spinner").hide();
        //             // setTimeout(function () {
        //             //     location.reload();
        //             // }, 1000);
        //         }
            // }
        })

    });
});


