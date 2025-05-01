if (typeof Omeka === 'undefined') {
    Omeka = {};
}

Omeka.Items = {};

(function ($) {
    /**
     * Enable drag and drop sorting for files.
     */
    Omeka.Items.enableSorting = function () {
        $('.sortable').sortable({
            items: 'li.file',
            forcePlaceholderSize: true,
            forceHelperSize: true,
            revert: 200,
            placeholder: "ui-sortable-highlight",
            containment: 'document',
            update: function (event, ui) {
                $(this).find('.file-order').each(function (index) {
                    $(this).val(index + 1);
                });
            }
        });
        $( ".sortable" ).disableSelection();

        $( ".sortable input[type=checkbox]" ).each(function () {
            $(this).css("display", "none");
        });
    };

    /**
     * Make links to files open in a new window.
     */
    Omeka.Items.makeFileWindow = function () {
        $('#file-list a').click(function (event) {
            event.preventDefault();
            if($(this).hasClass("delete")) {
                Omeka.Items.enableFileDeletion($(this));
            } else {
                window.open(this.getAttribute('href'));
            }
        });
    };

    /**
     * Set up toggle for marking files for deletion.
     */
    Omeka.Items.enableFileDeletion = function (deleteLink) {
        if( !deleteLink.next().is(":checked") ) {
            deleteLink.text("Undo").next().prop('checked', true).parents('.sortable-item').addClass("deleted");
        } else {
            deleteLink.text("Delete").next().prop('checked', false).parents('.sortable-item').removeClass("deleted");
        }
    };

    /**
     * Make the item type selector AJAX in the right item type form.
     *
     * @param {string} changeItemTypeUrl URL for getting form.
     * @param {string} itemId Item ID.
     */
    Omeka.Items.changeItemType = function (changeItemTypeUrl, itemId) {
        $('#change_type').hide();
        $('#item-type').change(function () {
            var params = {
                type_id: $(this).val()
            };
            if (itemId) {
                params.item_id = itemId;
            }
            $.ajax({
                url: changeItemTypeUrl,
                type: 'POST',
                dataType: 'html',
                data: params,
                success: function (response) {
                    var form = $('#type-metadata-form');
                    form.hide();
                    form.find('textarea').each(function () {
                        tinyMCE.EditorManager.execCommand('mceRemoveEditor', true, this.id);
                    });
                    form.html(response);
                    form.trigger('omeka:elementformload');
                    form.slideDown(0, function () {
                        // Explicit show() call fixes IE7
                        $(this).show();
                    });
                }
            });
        });
    };

    /**
     * Add remove/undo buttons for removing a tag.
     *
     * @param {string} tag Tag to add buttons for.
     */
    Omeka.Items.addTagElement = function (tag) {
        var tagLi = $('<li/>');
        tagLi.after(" ");

        $('<span></span>', {'class': 'tag', 'text': tag}).appendTo(tagLi);
        var undoButton = $('<span class="undo-remove-tag"><a href="#">Undo</a></span>').appendTo(tagLi);
        var deleteButton = $('<span class="remove-tag"><a href="#">Remove</a></span>').appendTo(tagLi);

        if($('#all-tags-list').length != 0) {
            $('#all-tags-list').append(tagLi);
        } else {
            $('#all-tags').append($('<h3>All Tags</h3><div class="tag-list"><ul id="all-tags-list"></ul></div>'));
            $('#all-tags-list').append(tagLi);
        }

        Omeka.Items.updateTagsField();
        return false;
    };


    /**
     * Add tag elements for new tags from the input box.
     *
     * @param {string} tags Comma-separated tags to be added.
     */
    Omeka.Items.addTags = function (tags) {
        var newTags = tags.split(Omeka.Items.tagDelimiter);

        // only add tags from the input box that are new
        var oldTags = $('.tag-list .tag').map(function () {
            return $.trim(this.textContent);
        });

        $.each(newTags, function () {
            var tag = $.trim(this);
            if (tag && $.inArray(tag, oldTags) === -1) {
                Omeka.Items.addTagElement(tag);
            }
        });

        $('#tags').val('');
    };

    /**
     * Callback for tag remove buttons.
     *
     * @param {Element} button Clicked button.
     */
    Omeka.Items.toggleTag = function (button) {
        $(button).parent().toggleClass('tag-removed');
        Omeka.Items.updateTagsField();
    };

    /**
     * Update the hidden tags fields to only include the tags that have not been removed.
     */
    Omeka.Items.updateTagsField = function () {
        var tagsToAdd = [];
        var tagsToDelete = [];

        $('.tag-list li').each(function () {
            var tagSpan = $(this).find('.tag');
            var tag = $.trim(tagSpan.text());
            if ($(this).hasClass('tag-removed')) {
                tagsToDelete.push(tag);
            } else {
                tagsToAdd.push(tag);
            }
        });

        $('#tags-to-add').val(tagsToAdd.join(Omeka.Items.tagDelimiter));
        $('#tags-to-delete').val(tagsToDelete.join(Omeka.Items.tagDelimiter));
    };

    /**
     * Set up tag remove/undo buttons and adding from tags field.
     *
     */
    Omeka.Items.enableTagRemoval = function () {
        $('#add-tags-button').click(function (event) {
            event.preventDefault();
            Omeka.Items.addTags($('#tags').val());
        });

        $(document).on('click', 'span.remove-tag', function (event) {
            event.preventDefault();
            Omeka.Items.toggleTag(this);
        });

        $(document).on('click', 'span.undo-remove-tag', function (event) {
            event.preventDefault();
            Omeka.Items.toggleTag(this);
        });

    };

    /**
     * Set up autocomplete for tags field.
     *
     * @param {string} inputSelector Selector for input to autocomplete on.
     * @param {string} tagChoicesUrl Autocomplete JSON URL.
     */
    Omeka.Items.tagChoices = function (inputSelector, tagChoicesUrl) {
        function split(val) {
            var escapedTagDelimiter = Omeka.Items.tagDelimiter.replace(/([.?*+\^$\[\]\\(){}\-])/g, "\\$1");
            var re = new RegExp(escapedTagDelimiter + '\\s*');
            return val.split(re);
        }
        function extractLast(term) {
            return split(term).pop();
        }

        // Tokenized input based on
        // http://jqueryui.com/demos/autocomplete/multiple.html
        $(inputSelector).autocomplete({
            source: function (request, response) {
                $.getJSON(tagChoicesUrl, {
                    term: extractLast(request.term)
                }, function (data) {
                    response(data);
                });
            },
            focus: function () {
                return false;
            },
            select: function (event, ui) {
                var terms = split(this.value);
                // remove the current input
                terms.pop();
                // add the selected item
                terms.push(ui.item.value);
                // add placeholder to get the comma-and-space at the end
                terms.push('');
                this.value = terms.join(Omeka.Items.tagDelimiter + ' ');
                return false;
            }
        });
    };

    /**
     * Submit tag changes on items/show with AJAX.
     */
    Omeka.Items.modifyTagsShow = function () {
        //Add the tags with this request
        $('#tags-form').submit(function (event) {
            event.preventDefault();
            var form = $(this);
            $.post(form.attr('action'), form.serialize(), function (response) {
                $('#tag-cloud').hide().html(response).fadeIn(1000);
            }, 'html');
        });
    };

    /**
     * Allow adding an arbitrary number of file input elements to the items form so that
     * more than one file can be uploaded at once.
     *
     * @param {string} label
     */
    Omeka.Items.enableAddFiles = function (label) {
        var filesDiv = $('#files-metadata .files');
        var fileInputIndex = 0;

        var getFileContainer = function() {
            return $(filesDiv.data('file-container-template').replace('__INDEX__', fileInputIndex++));
        };

        var humanFileSize = function(size) {
            const i = size == 0 ? 0 : Math.floor(Math.log(size) / Math.log(1024));
            return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
        }

        filesDiv.append(getFileContainer());

        // Handle an add file click.
        $('#add-file').on('click', function(e) {
            e.preventDefault();
            filesDiv.append(getFileContainer());
        });

        // Handle multiple file input.
        $(document).on('change', '.file-input', function(e) {

            const thisFileInput = $(this);
            const thisFileContainer = thisFileInput.closest('.file-container');

            // Iterate every file in the FileList.
            for (const [fileIndex, file] of Object.entries(this.files)) {

                let fileContainer;
                let fileInput;

                // Use the DataTransfer API to create a new FileList containing
                // one file, then set the FileList to this file input or an
                // additional file input if the original FileList contains more
                // than one file.
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                if (0 == fileIndex) {
                    // Add the first file to this file input.
                    fileContainer = thisFileContainer;
                    fileInput = thisFileInput;
                } else {
                    // Add each additional file to a new file input.
                    fileContainer = getFileContainer();
                    fileInput = fileContainer.find('.file-input');
                    filesDiv.append(fileContainer);
                }
                fileInput[0].files = dataTransfer.files;

                // Add the formatted file size.
                fileContainer.find('.file-size').text(humanFileSize(file.size));

                let thumbnail = fileContainer.find('.file-thumbnail');
                thumbnail.empty();
                // Add a thumbnail when the file is an image.
                if ((/^image\/(png|jpe?g|gif)$/).test(file.type)) {
                    const imageSrc = URL.createObjectURL(file);
                    const img = new Image();
                    img.onload = function() {
                        const maxSize = 100;
                        const smallestPercent = Math.min(maxSize / this.width, maxSize / this.height);
                        img.width = this.width * smallestPercent;
                        img.height = this.height * smallestPercent;
                        thumbnail.html(img);
                    }
                    img.src = imageSrc;
                }
            }
        });
    };
})(jQuery);
