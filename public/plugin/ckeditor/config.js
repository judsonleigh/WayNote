/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';

    config.skin = 'moono-lisa';

    config.toolbar = 'Full';
    config.toolbar = [
        ['Source','-','Templates'],
        ['Print', 'SpellChecker', 'Scayt'],
        ['Replace','-','RemoveFormat'],
        ['Styles','Format','Font','FontSize'],
        ['TextColor','BGColor'],
        ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
        ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
        ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
        ['Link','Unlink','Anchor'],
        ['Image','CodeSnippet','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak']
    ];

    config.filebrowserUploadUrl="/management-platform/file/uploadCk/";

    config.removeDialogTabs = 'image:advanced;link:advanced';

    config.font_names = '宋体/宋体;黑体/黑体;仿宋/仿宋_GB2312;楷体/楷体_GB2312;隶书/隶书;幼圆/幼圆;微软雅黑/微软雅黑;';
};
