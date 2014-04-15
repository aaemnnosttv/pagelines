PageLines 2.X
=============

PageLines 2.X is a fork of the [PageLines Framework](https://github.com/pagelines/PageLines-Framework) which was discontinued with the launch of [PageLines DMS](http://www.pagelines.com/dms/) on July 24th, 2013.


## How To Upgrade to 2.X

### Via WordPress Auto Updates (Recommended)

1. Download, install, and activate the [GitHub Updater](https://github.com/afragen/github-updater) plugin.
1. Edit the `style.css` file for the existing installation of PageLines you wish to upgrade found at `{wp-content}/themes/pagelines/style.css`.
1. Add the line `GitHub Theme URI: aaemnnosttv/pagelines` on its own line within the theme headers (for example, just under where it says *Theme Name*).
1. Save the file and go to *Dashboard > Updates* under wp-admin.
1. You should see an update for *PageLines 2.X* under Themes. (*Note: if you do not see an update right away, try refreshing the page a few times or click `Check Again` at the top.*)
1. Apply the update!

You will now continue to receive updates automatically as long as *GitHub Updater* is enabled.

### Manual Update (requires FTP access)

1. [Download the latest zipped release of PageLines 2.X](https://github.com/aaemnnosttv/pagelines/releases)
1. In wp-admin, go to *Appearance > Themes* and *Add New Theme*.
1. On the next screen, click *Upload* and select the file you just downloaded from GitHub (*eg: pagelines-2.4.6.zip*)
1. Via (FTP|SFTP|SSH) navigate to your `wp-content/themes` directory
1. Rename `pagelines` to `pagelines-244` and `pagelines-2.4.6` to `pagelines`
1. You've been updated!  You should now see *PageLines 2.X* under *Appearance > Themes*


# Support

Think you found a bug?  [Submit any issues here](https://github.com/aaemnnosttv/pagelines/issues)

Pull requests welcome!