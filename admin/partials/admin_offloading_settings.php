<?php

/**
 * Provide a settings view for the plugin
 *
 * This file is used to markup the settings page of the plugin.
 *
 * @link       https://publit.io
 * @since      1.0.0
 *
 * @package    Publitio
 */
?>

<div class="page-offloading-wrapper">
    <div class="section-offloading-wrapper">
        <h2 class="section-offloading-title">
            <img src="<?php echo PUBLITIO_OFFLOADING_PLUGIN_URL . 'admin/images/cloud-icon.png'; ?>" alt=""/>
            Publitio Offloading Settings
        </h2>

        
        <p class="download-text">
          This WordPress plugin offloads your media library to <a target="_blank" href="https://publit.io/">Publitio</a> 
        </p>

        <p class="download-text">
            Read tutorial <a target="_blank" href="https://publit.io/community/blog/wordpress-offloading-with-publitio">How to setup Publitio Offloading Wordpress Plugin</a>
        </p>

         <p class="download-text">
         Plugin is in <strong>BETA and testing</strong> phase. For support send us email at <a href="mailto:support@publit.io">support@publit.io</a>
         </p>

    </div>
    <div class="section-offloading-wrapper">
        <label class="form-offload-label" for="api-publitio-offloading-key">API key:</label>
        <input class="form-offload-input" id="api-publitio-offloading-key" name="api-publitio-offloading-key" type="password"
               value="<?php echo esc_html(get_option('publitio_offloading_key', '')); ?>"/>

        <label class="form-offload-label" for="api-publitio-offloading-secret">API secret:</label>
        <input class="form-offload-input" id="api-publitio-offloading-secret" name="api-publitio-offloading-secret" type="password"
               value="<?php echo esc_html(get_option('publitio_offloading_secret', '')); ?>"/>

        <div class="offloading-block error-offload-block" id="error-offload-block"></div>
        <div class="offloading-block success-offload-block" id="success-offload-block"></div>
    </div>

    <div class="button-section-offloading-wrapper">
        <button type="button" class="publitio-offload-button" id="update-offloading-button">Save Api Keys</button>
    </div>
    <hr />
    <div class="section-offloading-wrapper">
        <div class="off-margin">
            <label class="publitio-switch">
                <input type="checkbox"
                       id="allow-download" <?php echo esc_html((get_option('publitio_offloading_allow_download') && get_option('publitio_offloading_allow_download') === 'no' ? '' : 'checked')) ?>>
                <span class="publitio-slider round"></span>
            </label>
            <span class="download-text">Allow users to save/download media files</span>
        </div>
        <div class="off-margin">
            <label class="publitio-switch">
                <input type="checkbox"
                       id="offload-templates" <?php echo esc_html(get_option('publitio_offloading_offload_templates', 'yes') === 'no' ? '' : 'checked') ?>>
                <span class="publitio-slider round"></span>
            </label>
            <span class="download-text">Offload templates (header, footer, custom pages...)</span>
        </div>
        <div class="offloading-block error-offload-block" id="error-allow-block"></div>
        <div class="offloading-block success-offload-block" id="success-allow-block"></div>
    </div>
    <hr />
    <div class="section-offloading-wrapper">
        <label class="form-offload-label">Select files to be offloaded:</label>
        <div class="files-checkbox off-margin">
            <div class="offload-checkbox">
                <label class="publitio-switch">
                    <input type="checkbox"
                           id="image_checkbox" class="files-offload-input" <?php echo esc_html((get_option('publitio_offloading_image_checkbox') && get_option('publitio_offloading_image_checkbox') === 'no' ? '' : 'checked')) ?>>
                    <span class="publitio-slider round"></span>
                </label>
                <span class="download-text">Image</span>
            </div>
            <div class="offload-checkbox">
                <label class="publitio-switch">
                    <input type="checkbox"
                           id="video_checkbox" class="files-offload-input" <?php echo esc_html((get_option('publitio_offloading_video_checkbox') && get_option('publitio_offloading_video_checkbox') === 'no' ? '' : 'checked')) ?>>
                    <span class="publitio-slider round"></span>
                </label>
                <span class="download-text">Video</span>
            </div>
            <div class="offload-checkbox">
                <label class="publitio-switch">
                    <input type="checkbox"
                           id="audio_checkbox" class="files-offload-input" <?php echo esc_html((get_option('publitio_offloading_audio_checkbox') && get_option('publitio_offloading_audio_checkbox') === 'no' ? '' : 'checked')) ?>>
                    <span class="publitio-slider round"></span>
                </label>
                <span class="download-text">Audio</span>
            </div>
            <div class="offload-checkbox">
                <label class="publitio-switch">
                    <input type="checkbox"
                           id="document_checkbox" class="files-offload-input" <?php echo esc_html((get_option('publitio_offloading_document_checkbox') && get_option('publitio_offloading_document_checkbox') === 'no' ? '' : 'checked')) ?>>
                    <span class="publitio-slider round"></span>
                </label>
                <span class="download-text">Document (pdf)</span>
            </div>
        </div>
        <div class="offloading-block error-offload-block" id="error-checkbox-block"></div>
        <div class="offloading-block success-offload-block" id="success-checkbox-block"></div>
    </div>
    <br/>
    <div class="section-offloading-wrapper">
        <label class="form-offload-label" for="default-publitio-offloading-folder">Upload Folder:</label>
        <select class="form-offload-select" name="default-publitio-offloading-folder" id="default-publitio-offloading-folder"></select>

        <div class="offloading-block error-offload-block" id="folder-error-block"></div>
        <div class="offloading-block success-offload-block" id="folder-success-block"></div>
    </div>
    <br/>
    <div class="section-offloading-wrapper">
        <label class="form-offload-label" for="default-publitio-offloading-cname">Custom CNAME:</label>
        <select class="form-offload-select" name="default-publitio-offloading-cname" id="default-publitio-offloading-cname"></select>

        <div class="offloading-block error-offload-block" id="cname-error-block"></div>
        <div class="offloading-block success-offload-block" id="cname-success-block"></div>
    </div>
    <br/>
    <div class="section-offloading-wrapper">
        <label class="form-offload-label" for="offloading-image-quality">Image quality:</label>
        <select class="form-offload-select" name="offloading-image-quality" id="offloading-image-quality">
            <option value="100">100</option>
            <option value="90">90</option>
            <option selected value="80">80 (default)</option>
            <option value="70">70</option>
            <option value="60">60</option>
            <option value="50">50</option>
        </select>

        <div class="offloading-block error-offload-block" id="folder-error-image-quality"></div>
        <div class="offloading-block success-offload-block" id="folder-success-image-quality"></div>
    </div>
    <br/>
    <div class="section-offloading-wrapper">
        <label class="form-offload-label" for="offloading-video-quality">Video quality:</label>
        <select class="form-offload-select" name="offloading-video-quality" id="offloading-video-quality">
            <option value="1080">1080p</option>
            <option value="720">720p</option>
            <option selected value="480">480p</option>
            <option value="360">360p</option>
        </select>

        <div class="offloading-block error-offload-block" id="folder-error-video-quality"></div>
        <div class="offloading-block success-offload-block" id="folder-success-video-quality"></div>
    </div>
    <hr />
    <div class="section-offloading-wrapper">
        <p class="download-text">Plugin will automatically upload media from posts during editing & rendering. You can sync entire media library right away via this button - but please be patient, large media library can take some time to upload to Publitio. Use this if you have deleted files from Publitio, and need them re-uploaded. Proceed with caution!</p>
        <button class="sync-button" id="media-offload">Sync Now</button>
        <div class="offloading-block error-offload-block" id="media-upload-message-error"></div>
        <div class="offloading-block success-offload-block" id="media-upload-message-success"></div>
    </div>
    <hr />
    <div class="section-offloading-wrapper">
        <div class="offload-checkbox">
            <label class="publitio-switch">
                <input type="checkbox"
                       id="delete_checkbox" class="files-offload-delete" <?php echo esc_html((get_option('publitio_offloading_delete_checkbox') && get_option('publitio_offloading_delete_checkbox') === 'yes' ? 'checked' : '')) ?>>
                <span class="publitio-slider round"></span>
            </label>
            <span class="download-text">Delete file from Publitio when Media is deleted permanently</span>
        </div>
        <div class="offloading-block error-offload-block" id="error-delete-block"></div>
        <div class="offloading-block success-offload-block" id="success-delete-block"></div>
    </div>

    <hr />

    <div class="section-offloading-wrapper">

        <p class="danger-text">DANGER ZONE!</p>
        <p class="download-text">Options bellow are <strong>best to keep OFF</strong> (default). If you use them, <strong>plugin will delete files from local storage</strong> once they are uploaded to Publitio (useful if you have limited space within site) - but if you choose to <strong>deactivate Publitio Offloading plugin</strong> in the future, your site posts/pages <strong>will result in broken media links</strong> (as they are no longer present locally). <span class="danger-text">Use with caution & at your own risk as there is no going back once you use this options!</span>
        </p>
        <div class="off-margin">
            <label class="publitio-switch">
                <input type="checkbox" class="files-offload-delete"
                       id="replace_checkbox" <?php echo esc_html((get_option('publitio_offloading_replace_checkbox') && get_option('publitio_offloading_replace_checkbox') === 'yes' ? 'checked' : '')) ?>>
                <span class="publitio-slider round"></span>
            </label>
            <span class="download-text">Delete files from Media library once uploaded to Publitio.</span><span class="danger-text"> [Danger zone!]</span>
        </div>
        <div class="offloading-block error-offload-block" id="media-replace-message-error"></div>
        <div class="offloading-block success-offload-block" id="media-replace-message-success"></div>
    </div>
    <div class="section-offloading-wrapper">
        <button class="sync-button" id="media-delete">Delete All Offloaded Media </button> <span class="danger-text">[Danger zone!]</span> 
        <div class="offloading-block error-offload-block" id="media-delete-message-error"></div>
        <div class="offloading-block success-offload-block" id="media-delete-message-success"></div>
    </div>
    <div id="publitio-popup" class="overlay">
        <div class="offloading-popup">
            <div id="publitioProgress">
                <div id="loadPublitioNumber">0</div>
                <div id="publitioBar"></div>
            </div>
        </div>
    </div>
</div>
