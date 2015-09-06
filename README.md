WordPress XQR-Z100A
===================

Per default, inserting an unattached media item into a post via the media modal's 'Media Library' tab will automatically attach the inserted item to the post.

This plugin prevents that from happening. It unhooks the core `wp_send_attachment_to_editor()` function from the `wp_ajax_send-attachment-to-editor` action hook and replaces it with a near-identical copy of that function which does not automatically attach an unattached media item.