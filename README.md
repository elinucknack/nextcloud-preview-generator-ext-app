# Preview Generator Extension

This is the documentation of Generator Extension, an extension for the Nextcloud application Preview Generator!

Preview Generator Extension contains a job extending the application Preview Generator which pre-generats media previews. No extra CRON job is needed for this job. The Nextcloud job runs every 10 minutes.

The following steps describe the installation of Preview Generator Extension in Nextcloud.

## Prerequisite

- Nextcloud (version 27-28)

**Note:** Tested with Debian/Raspberry Pi OS

## Install the app

1. Copy the `previewgenerator_ext` into Nextcloud's `apps` folder.
2. Add the backup configuration to `config/config.php`:
   - `previewgenerator_ext_pre_generation_disabled`: Set to `false` if you want to enable the preview generation.

## How to use

1. Scheduled job: Runs every 10 minutes when the `previewgenerator_ext_pre_generation_disabled` is set to `false`.

## Authors

- [**Eli Nucknack**](mailto:eli.nucknack@gmail.com)
