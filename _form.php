<style>
    #pa-wrap table td, #pa-wrap table th {
        border: 0;
    }
</style>
<div id='pa-wrap'>
    <h3>Let's get a magic pixel art now</h3>
    <form action="options.php" method="post" id="pa_form">
        <?php wp_nonce_field('image-submission'); ?>
        <table class="form-table">
            <tbody>
            <tr>
                <th><label for="pa-img-input">Choose File</label></th>
                <td colspan="2">
                    <input type="file" name="pa_img" id="pa_img" class="image-file" accept="image/*" required>
                    <input type="hidden" name="pa_img_id"/>
                    <input type="hidden" name="action" value="image_submission"/>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <img id="pa_img_preview" src="#" alt="transform image" width="300" style="display:none;"/>
                </td>
                <td>
                    <a id="pa_img_result_wrap" href="#" target="_blank" style="display:none;">
                        <img id="pa_img_result" src="#" alt="transform image" width="300"/>
                    </a>
                </td>
            </tr>
            <tr>
                <th><label for="box_count">Count</label></th>
                <td colspan="2">
                    <input type="number" name="box_count" id="box_count" value="10"/>
                </td>
            </tr>
            <tr>
                <th><label for="box_size">Size</label></th>
                <td colspan="2">
                    <input type="number" name="box_size" id="box_size" value="0.5"/> cm
                </td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2">
                    <p class="submit"><input type="button" id="pa_btn_proceed" name="pa_btn_proceed" value="Proceed"/></p>
                </td>
            </tr>
            </tbody>
        </table>

    </form>
</div>