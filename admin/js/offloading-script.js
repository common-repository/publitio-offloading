(function ($) {
        'use strict';

        let timers = {
            recursiveTimeout : null,
            deleteTimeout : null

        };

        jQuery.extend({
            whenAll: function(expires, firstParam) {
                return whenAllFx(0, jQuery.makeArray(arguments));
            }
        });

        function whenAllFx(expires, args) {
            const def = jQuery.Deferred();
            let failed = false;
            const results = [];
            let to;

            if (expires) {
                to = setTimeout(function() {
                    def.reject('expired', results.slice(0), args);
                }, expires);
            }

            function next(remainingArgs) {
                if (remainingArgs.length) {
                    const arg = remainingArgs.shift();
                    results.push(arg);
                    jQuery.when(arg).fail(function() {
                        failed = true;
                    }).always(function() {
                        next(remainingArgs);
                    });
                }
                else {
                    to && clearTimeout(to);
                    if (failed) {
                        def.reject(results);
                    }
                    else {
                        def.resolve(results);
                    }
                }
            }

            next(args.slice(0));

            return def.promise();
        }

        const STATUSES = {
            ERROR_UNAUTHORIZED: 401,
            ERROR: 500,
            SUCCESS: 200
        }

        $(function () {
            getPublitioAccountSettings()
            updatePublitioSettingsButtonClick()
            updatePublitioDefaultFolderChange()
            updatePublitioDefaultCnameChange()
            changePublitioAllowDownload()
            changePublitioOffloadTemplates()
            changePublitioImageQuality()
            changePublitioVideoQuality()
            checkboxPublitioFiles()
            changePublitioDelete()
            syncPublitioMediaFiles()
            replacePublitioMedia()
            deletePublitioMediaFiles()
        });

        function updatePublitioSettingsButtonClick() {
            $('#update-offloading-button').bind('click', function (event) {
                clearBlocks();
                jQuery.post(ajaxurl, {
                    action: 'pwpo_update_offloading_settings',
                    api_secret: $('#api-publitio-offloading-secret').val(),
                    api_key: $('#api-publitio-offloading-key').val()
                }, function (response) {
                    if (response.status === STATUSES.ERROR_UNAUTHORIZED) {
                        showPublitioBlock($('#error-offload-block'), 'Wrong credentials');
                        clearFolderList(true);
                        clearCnameList(true);
                        setPublitioCheckBoxValue('allow-download', '');
                        setPublitioCheckBoxValue('offload-templates', '');
                        $(".form-offload-select").attr("disabled", true);
                        $(".files-offload-input").attr("disabled", true);
                        $(".form-offload-select").attr("disabled", true);
                        $(".files-offload-delete").attr("disabled", true);
                        $(".sync-button").attr("disabled", true);
                    } else if (response.status === STATUSES.SUCCESS) {
                        showPublitioBlock($('#success-offload-block'), 'Great!');
                        handleResponse(response);
                    } else {
                        showPublitioBlock($('#error-offload-block'), 'Something went wrong.');
                        clearFolderList(true);
                        clearCnameList(true);
                        setPublitioCheckBoxValue('allow-download', '');
                        setPublitioCheckBoxValue('offload-templates', '');
                        $(".form-offload-select").attr("disabled", true);
                        $(".files-offload-input").attr("disabled", true);
                        $(".form-offload-select").attr("disabled", true);
                        $(".files-offload-delete").attr("disabled", true);
                        $(".sync-button").attr("disabled", true);
                    }
                });
            });
        }

        function handleResponse(response) {

            //console.log("response.default_cname_url:: "+response.default_cname_url);

            if (response.folders != null) {
                $(".form-offload-select").removeAttr("disabled");
                $(".files-offload-input").removeAttr("disabled");
                $(".form-offload-select").removeAttr("disabled");
                $(".files-offload-delete").removeAttr("disabled");
                $(".sync-button").removeAttr("disabled");
                addFoldersList(response.folders, response.default_folder_id);
                addCnameList(response.cnames, response.default_cname_url);
                setPublitioCheckBoxValue('allow-download', response.allow_download);
                setPublitioCheckBoxValue('offload-templates', response.offload_templates);
                setPublitioImageQualityValue(response.image_quality);
                setPublitioVideoQualityValue(response.video_quality);
                setPublitioFilesCheckbox('image_checkbox', response.image_checkbox);
                setPublitioFilesCheckbox('video_checkbox', response.video_checkbox);
                setPublitioFilesCheckbox('audio_checkbox', response.audio_checkbox);
                setPublitioFilesCheckbox('document_checkbox', response.document_checkbox);
                setPublitioFilesCheckbox('delete_checkbox', response.delete_checkbox);
                setPublitioFilesCheckbox('replace_checkbox', response.replace_checkbox)
            } else {
                setPublitioCheckBoxValue('allow-download', '');
                setPublitioCheckBoxValue('offload-templates', '');
                $(".form-offload-delete").attr("disabled", true);
                $(".form-offload-select").attr("disabled", true);
                $(".files-offload-input").attr("disabled", true);
                $(".files-offload-delete").attr("disabled", true);
                $(".sync-button").attr("disabled", true);
            }
        }

        function getPublitioAccountSettings() {
            jQuery.get(ajaxurl, {action: 'pwpo_get_offloading_account_settings'}, function (response) {
                handleResponse(response);
            })
        }

        function addFoldersList(folders, defaultFolderId = '') {
            clearFolderList();
            if (folders !== undefined && folders !== null) {
                $('<option value="">/</option>').appendTo($('#default-publitio-offloading-folder'));
                folders.forEach((folder) => {
                    $('<option value="' + folder.id + '">' + folder.path + '</option>').appendTo($('#default-publitio-offloading-folder'));
                });
                setSelectedOffloadingFolder(defaultFolderId);
            }
        }

        function addCnameList(cnames, defaultCnameId = '') {
            clearCnameList();
            if (cnames !== undefined && cnames !== null) {
                //off via &wpo=true api call
                //$('<option value="">https://media.publit.io</option>').appendTo($('#default-publitio-offloading-cname'));
                cnames.forEach((cname) => {
                    $('<option value="' + cname.url + '">' + cname.url + '</option>').appendTo($('#default-publitio-offloading-cname'));
                })
                setSelectedOffloadingCname(defaultCnameId);
            }
        }

        function setSelectedOffloadingFolder(id) {
            $('#default-publitio-offloading-folder > option[value="' + id + '"]').attr("selected", "selected");
        }

        function setSelectedOffloadingCname(id) {
            $('#default-publitio-offloading-cname > option[value="' + id + '"]').attr("selected", "selected");
        }

        function clearFolderList(show = false) {
            $('#default-publitio-offloading-folder').empty();
            if (show === true) {
                $('<option selected hidden disabled>None</option>').appendTo($('#default-publitio-offloading-folder'));
            }
        }

        function clearCnameList(show = false) {
            $('#default-publitio-offloading-cname').empty();
            if (show === true) {
                $('<option selected hidden disabled>None</option>').appendTo($('#default-publitio-offloading-cname'));
            }
        }

        function clearBlocks() {
            $('#error-offload-block').empty();
            $('#success-offload-block').empty();
            $('#folder-success-block').empty();
            $('#folder-error-block').empty();
            $('#folder-success-image-quality').empty();
            $('#folder-error-image-quality').empty();
            $('#folder-success-video-quality').empty();
            $('#folder-error-video-quality').empty();
            $('#cname-success-block').empty();
            $('#cname-error-block').empty();
            $('#success-checkbox-block').empty();
            $('#error-checkbox-block').empty();
            $('#success-allow-block').empty();
            $('#error-allow-block').empty();
            $('#success-delete-block').empty();
            $('#error-delete-block').empty();
            $('#media-upload-message-success').empty();
            $('#media-upload-message-error').empty();
            $('#media-replace-message-error').empty();
            $('#media-replace-message-success').empty();
            $('#media-delete-message-success').empty();
            $('#media-delete-message-error').empty();
        }



        function showPublitioBlock(elem, content) {
            $(elem).html(content);
            var selector = elem.selector;
            if(timers && timers[selector]){
                clearTimeout(timers[selector]);
                timers[selector] = null;
            }
            timers[selector] = setTimeout(function () {
                clearBlocks();
            }, 3000);
        }

        function updatePublitioDefaultFolderChange() {
            $('#default-publitio-offloading-folder').bind('change', function (event) {
                jQuery.post(ajaxurl, {
                    action: 'pwpo_update_default_offloading_folder',
                    folder_id: event.target.value
                }, function (response) {
                    if (response.status === STATUSES.SUCCESS) {
                        showPublitioBlock($('#folder-success-block'), 'Great, default upload folder saved!');
                    } else {
                        showPublitioBlock($('#folder-error-block'), 'Something went wrong.');
                    }
                });
            });
        }

        function updatePublitioDefaultCnameChange() {
            $('#default-publitio-offloading-cname').bind('change', function (event) {
                jQuery.post(ajaxurl, {
                    action: 'pwpo_update_default_offloading_cname',
                    cname_url: event.target.value
                }, function (response) {
                    if (response.status === STATUSES.SUCCESS) {
                        showPublitioBlock($('#cname-success-block'), 'Great, default CNAME saved!');
                    } else {
                        showPublitioBlock($('#cname-error-block'), 'Something went wrong.');
                    }
                });
            });
        }

        function changePublitioImageQuality() {
            $("#offloading-image-quality").bind('change', function (event) {
                jQuery.post(ajaxurl, {
                        action: 'pwpo_update_image_offloading_quality',
                        image_quality: event.target.value
                    }, function (response) {
                        if (response.status === STATUSES.SUCCESS) {
                            showPublitioBlock($('#folder-success-image-quality'), 'Great, image quality updated!');
                        } else {
                            showPublitioBlock($('#folder-error-image-quality'), 'Something went wrong.');
                        }
                    }
                )
            })
        }

        function changePublitioVideoQuality() {
            $("#offloading-video-quality").bind('change', function (event) {
                jQuery.post(ajaxurl, {
                        action: 'pwpo_update_video_offloading_quality',
                        video_quality: event.target.value
                    }, function (response) {
                        if (response.status === STATUSES.SUCCESS) {
                            showPublitioBlock($('#folder-success-video-quality'), 'Great, video quality updated!');
                        } else {
                            showPublitioBlock($('#folder-error-video-quality'), 'Something went wrong.');
                        }
                    }
                )
            })
        }

        function checkboxPublitioFiles() {
            $(".files-offload-input").bind('change', function (event) {
                jQuery.post(ajaxurl, {
                        action: 'pwpo_update_files_checkbox',
                        id: event.target.id,
                        value: event.target.checked
                    }, function (response) {
                        if (response.status === STATUSES.SUCCESS) {
                            showPublitioBlock($('#success-checkbox-block'), 'Great!');
                        } else {
                            showPublitioBlock($('#error-checkbox-block'), 'Something went wrong.');
                        }
                    }
                )
            })
        }

        function changePublitioDelete() {
            $("#delete_checkbox").bind('change', function (event) {
                jQuery.post(ajaxurl, {
                        action: 'pwpo_update_delete_checkbox',
                        delete_checkbox: event.target.checked
                    }, function (response) {
                        if (response.status === STATUSES.SUCCESS) {
                            showPublitioBlock($('#success-delete-block'), 'Great!');
                        } else {
                            showPublitioBlock($('#error-delete-block'), 'Something went wrong.');
                        }
                    }
                )
            })
        }

        function replacePublitioMedia() {
            $("#replace_checkbox").bind('change', function (event) {
                if(event.target.checked === true) {
                    if (confirm('Are you sure you want to delete files from Media library once they are uploaded to Publitio? Plugin will delete files from local storage - but if you choose to deactivate Publitio Offloading plugin in the future, your site posts/pages will result in broken media links (as they are no longer present locally). Use with caution & at your own risk as there is no going back once you use this options!')) {
                        jQuery.post(ajaxurl, {
                                action: 'pwpo_update_replace_media',
                                replace_checkbox: event.target.checked
                            }, function (response) {
                                if (response.status === STATUSES.SUCCESS) {
                                    showPublitioBlock($('#media-replace-message-success'), 'Great!');
                                } else {
                                    showPublitioBlock($('#media-replace-message-error'), 'Something went wrong.');
                                }
                            }
                        )
                    } else {
                        $("#replace_checkbox").attr('checked', false);
                    }
                } else {
                    jQuery.post(ajaxurl, {
                            action: 'pwpo_update_replace_media',
                            replace_checkbox: event.target.checked
                        }, function (response) {
                            if (response.status === STATUSES.SUCCESS) {
                                showPublitioBlock($('#media-replace-message-success'), 'Great!');
                            } else {
                                showPublitioBlock($('#media-replace-message-error'), 'Something went wrong.');
                            }
                        }
                    )
                }
            })
        }

        function syncPublitioMediaFiles() {
            $('#media-offload').bind('click', function (event) {
                let media_list = null;
                jQuery.get(ajaxurl, {action: 'pwpo_get_media_list'}, function (response) {
                    media_list = response.media;
                    syncPublitioMedia(media_list);
                })
            })
        }

         function media_list_sync(mainList,media_list,index,resultInfo) {
            const requestList = [];
            media_list.forEach((media) => {
                requestList.push(
                    jQuery.post(ajaxurl,
                        {async:false,action: 'pwpo_sync_media_file',attach_id: media.ID},
                        function (responseMedia) {
                            if (responseMedia.sync === true) {
                                resultInfo.numOfUploaded++;
                            } else {
                                resultInfo.numOfFailed++;
                            }
                    }).fail(function() {
                        resultInfo.numOfFailed++;
                    }).always(function() {
                        let result = (((resultInfo.numOfUploaded + resultInfo.numOfFailed) / resultInfo.numOfMedia) * 100).toFixed(1);
                        $("#publitioBar").width(result + "%");
                        let resFailed = "";
                        if (resultInfo.numOfFailed !== 0) {
                            resFailed = ' <span class="red-text">(' + resultInfo.numOfFailed + ' failed)</span>';
                        }
                        $("#loadPublitioNumber").html(resultInfo.numOfUploaded + " of " + resultInfo.numOfMedia + resFailed + " / " + result + "% completed");
                    })
                );
            });

             $.whenAll(...requestList).done(function(x){
                 recursiveMediaLoading(mainList,index+1,resultInfo);
             }).fail(function(x) {
                 recursiveMediaLoading(mainList,index+1,resultInfo);
             })
        }

        function recursiveMediaLoading(media_list, index , resultInfo) {
            if(index < media_list.length) {
                media_list_sync(media_list,media_list[index],index,resultInfo);
            } else {
                if ((resultInfo.numOfUploaded+resultInfo.numOfFailed) === resultInfo.numOfMedia) {
                    if(timers && timers['recursiveTimeout']) {
                        clearTimeout(timers['recursiveTimeout']);
                        timers['recursiveTimeout'] = null;
                    }
                    timers['recursiveTimeout'] = setTimeout(function () {
                        $('#publitio-popup').hide();
                        $("#loadPublitioNumber").html(0);
                        $("#publitioBar").width("0%");
                        if(resultInfo.numOfFailed !== 0) {
                            showPublitioBlock($('#media-upload-message-success'), resultInfo.numOfUploaded +' synchronized successfully!' + '<span class="red-text"> ('+resultInfo.numOfFailed+' failed)</span>');
                        } else {
                            showPublitioBlock($('#media-upload-message-success'), 'Your media library is synchronized successfully!');
                        }

                    }, 1000)
                }
            }
        }


        function syncPublitioMedia(media_list) {
            if (media_list !== undefined && media_list !== null && media_list.length > 0) {
                if (confirm('Are you sure you want to synchronize all media files with Publitio?')) {
                    const resultInfo = {
                        numOfUploaded:0,
                        numOfFailed : 0,
                        numOfMedia : media_list.map((item) => item.length).reduce((a,b) => a+b,0)
                    };
                    $('#publitio-popup').show();
                    recursiveMediaLoading(media_list,0,resultInfo);
                }
            } else {
                showPublitioBlock($('#media-upload-message-success'), 'Your media library is already synchronized!');
            }
        }

        function deletePublitioMediaFiles() {
            $('#media-delete').bind('click', function (event) {
                let media_list = null;
                jQuery.get(ajaxurl, {action: 'pwpo_get_media_list_for_delete'}, function (response) {
                    media_list = response.media;
                    deletePublitioMedia(media_list);
                })
            })
        }

        function deletePublitioMedia(media_list) {
            if (media_list !== undefined && media_list !== null && media_list.length > 0) {
                if (confirm('Are you sure you want to delete all offloaded Media locally and replace it with Publitio Media URLs? Plugin will delete files from local storage - but if you choose to deactivate Publitio Offloading plugin in the future, your site posts/pages will result in broken media links (as they are no longer present locally). Use with caution & at your own risk as there is no going back once you use this options!')) {
                    let numOfDeleted = 0;
                    let numOfDeletedFailed = 0;
                    $('#publitio-popup').show();
                    let numOfMediaForDelete = media_list.length;
                    media_list.forEach((media) => {
                        jQuery.post(ajaxurl, {
                            async: false,
                            action: 'pwpo_delete_media_file',
                            attach_id: media.ID
                        }, function (responseMedia) {
                            if (responseMedia.deleted === true) {
                                numOfDeleted++;
                            } else {
                                numOfDeletedFailed++;
                            }

                            let result = (((numOfDeleted + numOfDeletedFailed) / numOfMediaForDelete) * 100).toFixed(1);
                            $("#publitioBar").width(result + "%");
                            $("#loadPublitioNumber").empty();
                            let resDeleteFailed = "";
                            if(numOfDeletedFailed !== 0 ) {
                                resDeleteFailed = '<span class="red-text"> ('+numOfDeletedFailed+' failed)</span>';
                            }
                            $("#loadPublitioNumber").html(numOfDeleted + " of "+ numOfMediaForDelete + resDeleteFailed  + " / " + result + "% completed");
                            if (numOfDeleted + numOfDeletedFailed === numOfMediaForDelete) {
                                if(timers && timers['deleteTimeout']) {
                                    clearTimeout(timers['deleteTimeout']);
                                    timers['deleteTimeout'] = null;
                                }
                                timers['deleteTimeout'] = setTimeout(function () {
                                    $('#publitio-popup').hide();
                                    $("#loadPublitioNumber").html(0);
                                    $("#publitioBar").width("0%");
                                    if(numOfDeletedFailed !== 0) {
                                        showPublitioBlock($('#media-delete-message-success'), numOfDeleted +' deleted successfully!' + '<span class="red-text"> ('+numOfDeletedFailed+' failed)</span>');
                                    } else {
                                        showPublitioBlock($('#media-delete-message-success'), 'All media files are deleted successfully!');
                                    }

                                }, 1000)
                            }
                        })
                    })
                }
            } else {
                showPublitioBlock($('#media-delete-message-success'), 'All media files are already deleted!');
            }
        }


        function changePublitioAllowDownload() {
            $('#allow-download').bind('change', function (event) {
                jQuery.post(ajaxurl, {
                    action: 'pwpo_update_allow_download',
                    allow: event.target.checked
                }, function (response) {
                    if (response.status === STATUSES.SUCCESS) {
                        showPublitioBlock($('#success-allow-block'), 'Great!');
                    } else {
                        showPublitioBlock($('#error-allow-block'), 'Something went wrong.');
                    }
                });
            });
        }

        function changePublitioOffloadTemplates() {
            $('#offload-templates').bind('change', function (event) {
                jQuery.post(ajaxurl, {
                    action: 'pwpo_update_offload_templates',
                    allow: event.target.checked
                }, function (response) {
                    if (response.status === STATUSES.SUCCESS) {
                        showPublitioBlock($('#success-allow-block'), 'Great!');
                    } else {
                        showPublitioBlock($('#error-allow-block'), 'Something went wrong.');
                    }
                });
            });
        }

        function setPublitioCheckBoxValue(id,allow) {
            if (allow !== '') {
                setPublitioCheckBoxDisabled(id, false);
                if (allow === 'no') {
                    $("#"+id).attr('checked', false);
                } else {
                    $("#"+id).attr('checked', true);
                }
            } else {
                setPublitioCheckBoxDisabled(id, true);
            }
        }

        function setPublitioCheckBoxDisabled(id, value) {
            if (value) {
                $("#" + id).attr("disabled", true);
            } else {
                $("#" + id).removeAttr("disabled");
            }
        }

        function setPublitioImageQualityValue(quality) {
            if (!quality || quality === "") {
                quality = '80';
            }
            $('#offloading-image-quality > option[value="' + quality + '"]').attr("selected", "selected");
        }

        function setPublitioVideoQualityValue(quality) {
            if (!quality || quality === "") {
                quality = '480';
            }
            $('#offloading-video-quality > option[value="' + quality + '"]').attr("selected", "selected");
        }

        function setPublitioFilesCheckbox(id, value) {
            if ((!value || value === "" || value === 'yes') && (id !== 'delete_checkbox') && (id !== 'replace_checkbox')) {
                $("#" + id).attr('checked', true);
            } else if (id === 'delete_checkbox' || id === 'replace_checkbox') {
                if (value === "yes") {
                    $("#" + id).attr('checked', true);
                } else {
                    $("#" + id).attr('checked', false);
                }
            } else {
                $("#" + id).attr('checked', false);
            }
        }
    }

)(jQuery);

