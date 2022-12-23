# Hull Seals Paperwork System

This is the repository for the Hull Paperwork System.

# Description

This repository houses all of the files required to build and host your own version of the Hull Seals Paperwork System. The system is how we automate our paperwork and records-keeping process, which allows us to accurately track cases and repairs, participants, and clients in an easily-storable, recallable format.

# Installation

## Requirements

- PHP 5.5+ (7.x+ Recommended)
- An SQL Server with tables to store data (Not Provided)
- A Web server software such as Apache2 or NGIX.
- A JavaScript-enabled browser.
- Some Site-Wide Assets found in our [Main Site Repository](https://gitlab.com/hull-seals/code/hull-seals-main-site).

## Usage

To install, download the latest [release](https://gitlab.com/hull-seals/code/website-subsections/paperwork/-/releases) from our repository. Upload and extract the files to the directory or subdirectory you wish to install from, and change the information in `db.php` and `auth.php` to fit your server. Ensure that you have created Stored Procedures and have the appropriate tables. Due to security risks, our own example tables are not provided.

## Troubleshooting

- Upon installation, be sure to replace the information in `db.php` and `auth.php` to match your own details.
- Additionally, be sure to create a database and tables, and method of creating, updating, and removing data. It is encouraged to use Stored Procedures for this task.
- If you are having issues, look through the closed bug reports.
- If no issue is similar, open a new bug report. Be sure to be detailed.

# Support

The best way to receive support is through the issues section of this repository. As every setup is different, support may be unable to help you, but in general we will try when we can.
If for some reason you are unable to do so, emailing us at Code[at]hullseals[dot]space will also reach the same team.

# Roadmap

In the medium term, it is likely that we will be loading some client data early in the process, and loading in known good data in a case-by-case basis to help speed up paperwork.

As always, bugfixes, speed, and stability updates are priorities as discovered, as well as general enhancements over time.

# Contributing

Interested in joining the Hull Seals Cyberseals? Read up on [the Welcome Board](https://gitlab.com/hull-seals/welcome).

# Authors and Acknowledgements

The majority of this code was written by [David Sangrey](https://gitlab.com/Rixxan).

Many thanks to all of our [Contributors](https://gitlab.com/hull-seals/welcome/blob/master/CONTRIBUTORS.md).

Layout design by [Wolfii Namakura](https://gitlab.com/wolfii1), implemented by [David Sangrey](https://gitlab.com/Rixxan).

# License

This project is governed under the [GNU General Public License v3.0](LICENSE) license.

# Project Status

This project is in a RELEASE state, with no major changes coming up immediately.
