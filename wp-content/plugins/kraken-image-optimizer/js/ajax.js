jQuery(document).ready(function($) {

    var errors = [{
        code: 401,
        msg: 'Unnknown API Key. Please check your API key and try again'
    }, {
        code: 403,
        msg: 'Your account has been temporarily suspended'
    }, {
        code: 413,
        msg: 'File size too large. The maximum file size for your plan is 1048576 bytes'
    }, {
        code: 415,
        msg: 'File type not supported'
    }, {
        code: 415,
        msg: 'WebP compression is non available for SVG images'
    }, {
        code: 422,
        msg: 'You need to specify either callback_url or wait flag'
    }, {
        code: 422,
        msg: 'This image can not be optimized any further'
    }, {
        code: 500,
        msg: 'Kraken has encountered an unexpected error and cannot fulfill your request'
    }, {
        code: 502,
        msg: 'Couldn\'t get this file'
    }];


    $('.krakenWhatsThis').tipsy({
        fade: true,
        gravity: 'w'
    });

    var data = {
            action: 'kraken_request'
        },

        errorTpl = '<div class="krakenErrorWrap"><a class="krakenError">Failed! Hover here</a></div>',
        $btnApplyBulkAction = $("#doaction"),
        $btnApplyBulkAction2 = $("#doaction2"),
        $topBulkActionDropdown = $(".tablenav.top .bulkactions select[name='action']"),
        $bottomBulkActionDropdown = $(".tablenav.bottom .bulkactions select[name='action2']");


    var requestSuccess = function(data, textStatus, jqXHR) {
        var $button = $(this),
            $parent = $(this).parent(),
            $cell = $(this).closest("td");

        if (data.success && typeof data.error === 'undefined') {

            $button.text("Image optimized");

            var type = data.type,
                krakedSize = data.kraked_size,
                originalSize = data.original_size,
                savingsPercent = data.savings_percent,
                $originalSizeColumn = $(this).parent().prev("td.original_size"),
                krakedData = '';

            $parent.fadeOut("fast", function() {
                $cell.find(".noSavings, .krakenErrorWrap").remove();
                $(this).replaceWith(data.html);
                $originalSizeColumn.html(originalSize);
                $parent.remove();
            });

        } else if (data.error) {

            var $error = $(errorTpl).attr("title", data.error);

            $parent
                .closest("td")
                .find(".krakenErrorWrap")
                .remove();


            $parent.after($error);
            $error.tipsy({
                fade: true,
                gravity: 'e'
            });

            $button
                .text("Retry request")
                .removeAttr("disabled")
                .css({
                    opacity: 1
                });
        }
    };

    var requestFail = function(jqXHR, textStatus, errorThrown) {
        $(this).removeAttr("disabled");
    };

    var requestComplete = function(jqXHR, textStatus, errorThrown) {
        $(this).removeAttr("disabled");
        $(this)
            .parent()
            .find(".krakenSpinner")
            .css("display", "none");
    };

    var opts = '<option value="kraken-bulk-lossy">' + "Krak 'em all" + '</option>';

    $topBulkActionDropdown.find("option:last-child").before(opts);
    $bottomBulkActionDropdown.find("option:last-child").before(opts);


    var getBulkImageData = function() {
        var $rows = $("tr[id^='post-']"),
            $row = null,
            postId = 0,
            imageDateItem = {},
            $krakBtn = null,
            btnData = {},
            originalSize = '',
            rv = [];
        $rows.each(function() {
            $row = $(this);
            postId = this.id.replace(/^\D+/g, '');
            if ($row.find("input[type='checkbox'][value='" + postId + "']:checked").length) {
                $krakBtn = $row.find(".kraken_req");
                if ($krakBtn.length) {
                    btnData = $krakBtn.data();
                    originalSize = $.trim($row.find('td.original_size').text());
                    btnData.originalSize = originalSize;
                    rv.push(btnData);
                }
            }
        });
        return rv;
    };

    var renderBulkImageSummary = function(bulkImageData) {
        var modalOptions = {
                zIndex: 4,
                escapeClose: true,
                clickClose: false,
                closeText: 'close',
                showClose: false
            },
            setting = kraken_settings.api_lossy,
            nImages = bulkImageData.length,
            header = '<p class="krakenBulkHeader">Kraken Bulk Image Optimization</p>',
            krakEmAll = '<button class="kraken_req_bulk">Krak \'em all</button>',
            typeRadios = '<span class="radiosWrap"><span class="kraken-bulk-choose-type">Choose:</span>' + '<input type="radio" id="kraken-bulk-type-lossy" value="Lossy" name="kraken-bulk-type"/>' + '<label for="kraken-bulk-type-lossy">Lossy</label>&nbsp;' + '<input type="radio" id="kraken-bulk-type-lossless" value="Lossless" name="kraken-bulk-type"/>' + '<label for="kraken-bulk-type-lossless">Lossless</label></span>',
            $modal = $('<div id="kraken-bulk-modal" class="kraken-modal"></div>')
            .html(header)
            .append(typeRadios)
            .append('<br /><small class="kraken-bulk-small">The following <strong>' + nImages + '</strong> images will be optimized by Kraken.io using the <strong class="bulkSetting">' + setting + '</strong> setting:</small><br />')
            .appendTo("body")
            .kmodal(modalOptions)
            .bind($.kmodal.BEFORE_CLOSE, function(event, modal) {

            })
            .bind($.kmodal.OPEN, function(event, modal) {

            })
            .bind($.kmodal.CLOSE, function(event, modal) {
                $("#kraken-bulk-modal").remove();
            })
            .css({
                top: "10px",
                marginTop: "40px"
            });

        if (setting === 'lossy') {
            $("#kraken-bulk-type-lossy").attr("checked", true);
        } else {
            $("#kraken-bulk-type-lossless").attr("checked", true);
        }

        $bulkSettingSpan = $(".bulkSetting");
        $("input[name='kraken-bulk-type']").change(function() {
            var text = this.id === "kraken-bulk-type-lossy" ? "lossy" : "lossless";
            $bulkSettingSpan.text(text);
        });

        // to prevent close on clicking overlay div
        $(".jquery-modal.blocker").click(function(e) {
            return false;
        });

        // otherwise media submenu shows through modal overlay
        $("#menu-media ul.wp-submenu").css({
            "z-index": 1
        });

        var $table = $('<table id="kraken-bulk"></table>'),
            $headerRow = $('<tr class="kraken-bulk-header"><td>File</td><td style="width:120px">Original Size</td><td style="width:120px">Kraked Size</td><td style="width:120px">Savings</td><td style="width:120px">% Savings</td></tr>');

        $table.append($headerRow);
        $.each(bulkImageData, function(index, element) {
            $table.append('<tr class="kraken-item-row" data-krakenbulkid="' + element.id + '"><td class="kraken-filename">' + element.filename + '</td><td class="kraken-originalsize">' + element.originalSize + '</td><td class="kraken-krakedsize"><span class="krakenBulkSpinner hidden"></span></td><td class="kraken-savings"></td><td class="kraken-savingsPercent"></td></tr>');
        });

        $modal
            .append($table)
            .append(krakEmAll)
            .append('<span class="close-kraken-bulk">Close Window</span>');

        $(".close-kraken-bulk").click(function() {
            $.kmodal.close();
        });

        if (!nImages) {
            $(".kraken_req_bulk")
                .attr("disabled", true)
                .css({
                    opacity: 0.5
                });
        }
    };

    var bulkAction = function(bulkImageData) {

        $bulkTable = $("#kraken-bulk");
        var jqxhr = null;

        var q = async.queue(function(task, callback) {
            var id = task.id,
                filename = task.filename;

            var $row = $bulkTable.find("tr[data-krakenbulkid='" + id + "']"),
                $krakedSizeColumn = $row.find(".kraken-krakedsize"),
                $spinner = $krakedSizeColumn
                .find(".krakenBulkSpinner")
                .css({
                    display: "inline-block"
                }),
                $savingsPercentColumn = $row.find(".kraken-savingsPercent"),
                $savingsBytesColumn = $row.find(".kraken-savings");

            jqxhr = $.ajax({
                url: ajax_object.ajax_url,
                data: {
                    'action': 'kraken_request',
                    'id': id,
                    'type': $("input[name='kraken-bulk-type']:checked").val().toLowerCase()
                },
                type: "post",
                dataType: "json",
                timeout: 360000
            })
                .done(function(data, textStatus, jqXHR) {
                    if (data.success && typeof data.error === 'undefined') {
                        var type = data.type,
                            originalSize = data.original_size,
                            krakedSize = data.kraked_size,
                            savingsPercent = data.savings_percent,
                            savingsBytes = data.saved_bytes;

                        $krakedSizeColumn.text(krakedSize);
                        $savingsPercentColumn.text(savingsPercent);
                        $savingsBytesColumn.text(savingsBytes);

                        var $button = $("button[id='krakenid-" + id + "']"),
                            $parent = $button.parent(),
                            $cell = $button.closest("td"),
                            $originalSizeColumn = $button.parent().prev("td.original_size")


                        $parent.fadeOut("fast", function() {
                            $cell.find(".noSavings, .krakenErrorWrap").remove();
                            krakedData = '<strong>' + krakedSize + '</strong><br /><small>Type:&nbsp;' + type + '</small><br /><small>Savings: ' + savingsPercent + '</small>';
                            if (typeof data.thumbs_data !== 'undefined') {
                                krakedData += '<br /><small>' + data.thumbs_data.length + ' thumbs optimized</small>';
                            }
                            $(this).replaceWith(krakedData);
                            $originalSizeColumn.html(originalSize);
                            $parent.remove();
                        });

                    } else if (data.error) {
                        if (data.error === 'This image can not be optimized any further') {
                            $krakedSizeColumn.text('No savings found.');
                        } else {

                        }
                    }

                })

            .fail(function() {

            })

            .always(function() {
                $spinner.css({
                    display: "none"
                });
                callback();
            });
        }, kraken_settings.bulk_async_limit);

        q.drain = function() {
            $(".kraken_req_bulk")
                .removeAttr("disabled")
                .css({
                    opacity: 1
                })
                .text("Done")
                .unbind("click")
                .click(function() {
                    $.kmodal.close();
                });
        }

        // add some items to the queue (batch-wise)
        q.push(bulkImageData, function(err) {

        });
    };


    $btnApplyBulkAction.add($btnApplyBulkAction2)
        .click(function(e) {
            if ($(this).prev("select").val() === 'kraken-bulk-lossy') {
                e.preventDefault();
                var bulkImageData = getBulkImageData();
                renderBulkImageSummary(bulkImageData);

                $('.kraken_req_bulk').click(function(e) {
                    e.preventDefault();
                    $(this)
                        .attr("disabled", true)
                        .css({
                            opacity: 0.5
                        });
                    bulkAction(bulkImageData);
                });
            }
        });

    $('body').on('click', 'small.krakenReset', function(e) {
        e.preventDefault();
        var $resetButton = $(this);
        var resetData = {
            action: 'kraken_reset'
        };
        resetData.id = $(this).data("id");

        var $spinner = $('<span class="resetSpinner"></span>');
        $resetButton.after($spinner);

        var jqxhr = $.ajax({
                url: ajax_object.ajax_url,
                data: resetData,
                type: "post",
                dataType: "json",
                timeout: 360000
            })
            .done(function(data, textStatus, jqXHR) {
                if (data.success !== 'undefined') {
                    $resetButton
                        .closest('.kraked_size.column-kraked_size')
                        .hide()
                        .html(data.html)
                        .fadeIn()
                        .prev(".original_size.column-original_size")
                        .html(data.original_size);
                }
            });
    });

    $('body').on('click', '.kraken-reset-all', function(e) {
        e.preventDefault();

        var reset = confirm('This will immediately remove all Kraken metadata associated with your images. \n\nAre you sure you want to do this?');
        if (!reset) {
            return;
        }

        var $resetButton = $(this);
        $resetButton
            .text('Resetting images, pleaes wait...')
            .attr('disabled', true);
        var resetData = {
            action: 'kraken_reset_all'
        };


        var $spinner = $('<span class="resetSpinner"></span>');
        $resetButton.after($spinner);

        var jqxhr = $.ajax({
                url: ajax_object.ajax_url,
                data: resetData,
                type: "post",
                dataType: "json",
                timeout: 360000
            })
            .done(function(data, textStatus, jqXHR) {
                $spinner.remove();
                $resetButton
                    .text('Your images have been reset.')
                    .removeAttr('disabled')
                    .removeClass('enabled');
            });
    });

    $('.krakenAdvancedSettings h3').on('click', function () {
        var $rows = $('.kraken-advanced-settings');
        var $plusMinus = $('.kraken-plus-minus');
        if ($rows.is(':visible')) {
            $rows.hide();
            $plusMinus
                .removeClass('dashicons-arrow-down')
                .addClass('dashicons-arrow-right');
        } else {
            $rows.show();
            $plusMinus
                .removeClass('dashicons-arrow-right')
                .addClass('dashicons-arrow-down');        
        }
    });

    $('body').on("click", ".kraken_req", function(e) {
        e.preventDefault();
        var $button = $(this),
            $parent = $(this).parent();

        data.id = $(this).data("id");

        $button
            .text("Optimizing image...")
            .attr("disabled", true)
            .css({
                opacity: 0.5
            });


        $parent
            .find(".krakenSpinner")
            .css("display", "inline");


        var jqxhr = $.ajax({
            url: ajax_object.ajax_url,
            data: data,
            type: "post",
            dataType: "json",
            timeout: 360000,
            context: $button
        })

        .done(requestSuccess)

        .fail(requestFail)

        .always(requestComplete);

    });
});