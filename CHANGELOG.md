# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.6.2] - 2024-03-20
### Security
- Updated Content Security Policy (CSP) to properly handle TinyMCE stylesheets
- Improved security headers configuration for better CDN resource handling
- Enhanced CSP directives for better compatibility with external resources

### Technical
- Refactored security headers middleware for better maintainability
- Updated CSP to use domain-level permissions for TinyMCE resources
- Improved documentation of security headers implementation

## [0.60] - 2025-01-11
### Added
- Teacher profile system with modal view
- Linked teachers to courses with profile information
- Added teacher profile management in admin dashboard
- Added profile picture support for teacher profiles
- Added detailed teacher information display (education, contact info, etc.)

### Changed
- Updated course form to include teacher profile selection
- Modified teacher display in course listings to show profile modal
- Enhanced teacher information display throughout the application

### Technical
- Added new JavaScript module for handling teacher profile interactions
- Implemented API endpoint for fetching teacher profile data
- Added profile picture upload and management functionality

## [0.5.0] - 2024-03-19
### Added
- Folksonomy tagging system for daily notes
- Tag cloud view for course topics
- Ability to view all notes with the same topic/tag
- Automatic tag creation from note topics

### Changed
- Admin dashboard now links section names directly to their daily notes
- Improved topic display in daily notes view
- Admin users now redirect to dashboard upon login

## [0.4.0] - 2024-03-18
### Added
- Weekly planning system
- Academic year management
- Course sections management
- Basic admin dashboard

## [0.3.0] - 2024-03-17
### Added
- Daily notes feature
- Course management system
- Section management within courses
- Basic user roles (admin/user)

## [0.2.0] - 2024-03-16
### Added
- User authentication system
- Basic course viewing functionality
- Initial admin controls

## [0.1.0] - 2024-03-15
### Added
- Initial project setup
- Basic routing system
- Database structure
- Core MVC framework 

## [0.6.1] - 2025-01-12
### Added
- Pre-fill new daily note content with the previous note's content if available.
- Integrated TinyMCE for rich text editing in note creation and editing.

### Fixed
- Resolved issue with undefined variable `$lastNote` in note creation view.

### Technical
- Updated `createNote` method in `AdminController` to fetch the last note for a section.
- Added `getLastBySection` method in `Note` model to retrieve the last note for a section. 