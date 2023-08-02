# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased
### Added
- Additional classes and scripts for managing configuration, modeling, and database interactions
- SQL scripts for setup/teardown of the new database structure
- Masquerading functionality for developers

### Changed
- Refactoring database structure
- Configuration moved into INI files under the `config/` directory
- Database and authentication provider IDs and secrets moved out of the public directory and into INI files
