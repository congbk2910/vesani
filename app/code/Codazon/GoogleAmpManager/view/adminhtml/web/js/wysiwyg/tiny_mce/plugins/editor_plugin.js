/**
 * Added wysiwyg plugin for tinymce global object
 */

require([
    'tiny_mce_4/tinymce.min',
    ], function(tinymce){
        tinymce.create('tinymce.plugins.cdz_amp_image', {
            ampPlaceholder: '\n<div id="amp-content-placeholder" style="display: none;">&nbsp;</div>',
            ampPlaceholderId: 'amp-content-placeholder',
            bookmark: '<span id="mce_marker" data-mce-type="bookmark">﻿</span>',
            /**
             * Initialize editor plugin.
             *
             * @param {tinymce.editor} editor - Editor instance that the plugin is initialized in.
             * @param {String} url - Absolute URL to where the plugin is located.
             */
            init: function (editor, url) {
                var self = this;
                if (editor.id.includes("_amp_")) {
                    editor.on('BeforeSetContent', function(e) {
                        if (e.target.id = editor.id) {
                            var content = e.content;
                            if (!content.includes(self.ampPlaceholderId)) {
                                if ((content != self.bookmark) && (content != '')) {
                                    content = content.gsub(/<amp-img(.*?)\/amp-img>/i, function (match) {
                                        return match[0].replace('<amp-img', '<img')
                                            .replace('></amp-img>', '>')
                                            .replace(' layout=', ' data-mce-layout=');
                                    }) + self.ampPlaceholder;
                                    e.content = content;
                                }
                            }
                        }
                    });
                    varienGlobalEvents.attachEventHandler('wysiwygDecodeContent', function (content) {
                        content = self.decodeImages(content);
                        return content;
                    });
                }
            },
            /**
             * Decode images attributes in content.
             *
             * @param {String} content
             * @returns {String}
             */
            decodeImages: function (content) {
                var self = this;
                console.log(content);
                if (content.includes(self.ampPlaceholderId)) {
                    var ampPlaceholder = new RegExp(self.ampPlaceholder, 'g');
                    return content.gsub(/<img(.*?)>/i, function (match) {
                        var attr = (match[0].search('data-mce-layout') == -1) ? ' layout="responsive" ':'';
                        return match[0].replace('<img', '<amp-img')
                            .replace('>', attr + '></amp-img>')
                            .replace(' data-mce-layout=', ' layout=');
                    }).replace(ampPlaceholder, '');
                }
                return content;
            }
        });

        // Register plugin
        tinymce.PluginManager.add('cdz_amp_image', tinymce.plugins.cdz_amp_image);
});