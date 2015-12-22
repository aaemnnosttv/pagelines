PL 2.X Changelog
================

#### 2.5.1
- Fix error with WP CLI

#### 2.5.0

- Separate compiled LESS into 3 files (core, sections, extended)
- Add `pl2x` WP_CLI command, currently allows for purging cache `wp pl2x purge`
- Various fixes and optimizations

#### 2.4.8

- Sucuri [security update](http://blog.sucuri.net/2015/01/security-advisory-vulnerabilities-in-pagelinesplatform-theme-for-wordpress.html)

#### 2.4.7

- Add support for [Composer](http://getcomposer.org/)
- Fix undefined property notice

#### 2.4.6

- Sections now detected within plugins (just like DMS)
- Fix a rare condition that could break compiled less output
- Sidebars upgraded to protect against a bug which could create problems in rare cases
- Misc cleanup and optimization

#### 2.4.5.3

- Fix login image styling
- Add `site-wrap` class to `#site`
- Add chromeframe meta tag
- Hide core html source comments by default (enable `PL_DEV` to show)

#### 2.4.5.2

- Admin menu icon fix
- Account/dashboard cleanup
- Allow `$oset` options to be passed in PageLinesSection `opt()` method

#### 2.4.5.1

- Fix many php notices
- Misc style fixes
- Integration with [GitHub Updater](https://github.com/afragen/github-updater)
