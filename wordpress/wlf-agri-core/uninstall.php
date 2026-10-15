<?php
/** Products and enquiries are business records. Retain them on uninstall. */
defined('WP_UNINSTALL_PLUGIN') || exit;
// To remove records, explicitly delete them using WordPress before uninstalling.
