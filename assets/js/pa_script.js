function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            jQuery('#pa_img_preview').attr('src', e.target.result);
            jQuery('#pa_img_preview').css("display", "inline");
        }

        reader.readAsDataURL(input.files[0]);
    }
}
jQuery(document).ready(function() {
    var $formNotice = jQuery('.form-notice');
    var $imgForm = jQuery('#pa_form');
    var $imgNotice = $imgForm.find('.image-notice');
    var $imgPreview = $imgForm.find('#pa_img_preview');
    var $imgFile = $imgForm.find('#pa_img');
    var $imgId = $imgForm.find('[name="pa_img_id"]');

    jQuery("#pa_btn_proceed").click(function(e) {

        e.preventDefault();

        if ($imgFile[0].files.length < 1) {
            alert("Please select the image.");
            return;
        }

        jQuery("#pa_btn_proceed").attr('disabled', true);

        var formData = new FormData();

        formData.append('action', 'image_submission');
        formData.append('async-upload', $imgFile[0].files[0]);
        formData.append('name', $imgFile[0].files[0].name);
        formData.append('_wpnonce', pa_config.nonce);
        formData.append('box_count', jQuery("#box_count").val());
        formData.append('box_size', jQuery("#box_size").val());

        jQuery.ajax({
            url: pa_config.ajax_url,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            type: 'POST',
            success: function(res) {

                if (res.success) {
                    $imgNotice.html('Successfully uploaded.');
                    jQuery("#pa_img_result").attr('src', res.data.url);
                    jQuery("#pa_img_result_wrap").show();
                    jQuery("#pa_img_result_wrap").attr('href', res.data.url);
                } else {
                    $imgNotice.html('Fail to upload image. Please try again.');
                }
                jQuery("#pa_btn_proceed").attr('disabled', false);
            }
        });
    });

    jQuery("#pa_img").change(function(e) {
        e.preventDefault();

        readURL(this);
    });


});