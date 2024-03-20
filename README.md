# Simple Document Management System using Laravel

This is a simple document management system built with Laravel. It allows for the monitoring and version control of documents through a pipeline consisting of uploader, reviewer, and finalizer steps.

## Features

- **Document Pipeline:** Documents go through a series of steps in the pipeline.
- **Notification System:** Automatic email notifications are sent to relevant parties at each step.
- **Version Control:** Track document versions and their statuses.

## Pipeline Steps

1. **Uploader Uploads File:**
    - The uploader uploads the document.
    - A notification email is sent to reviewers for their review.

2. **Reviewer Reviews Document:**
    - Reviewers examine the document and decide whether to accept or reject it.
    - If accepted, the document proceeds to the finalizer step.
    - If rejected, the document status is set to rejected, and it needs to be edited or deleted.

3. **Finalizer Reviews Document:**
    - Finalizers review the document one last time.
    - If accepted, the document status is set to approved.
    - If rejected, the document is sent back to the reviewer with a reason for rejection.


## Email Notifications

- **Uploader Notification:**
    - Upon document upload, reviewers are notified.

- **Reviewer Notification:**
    - When a document is ready for review, reviewers receive a notification.

- **Finalizer Notification:**
    - Finalizers are notified when a document requires their review.

- **Rejection Notification:**
    - Whenever a document is rejected, the relevant party receives an email with a reason for rejection.

## Installation

1. Clone the repository: `git clone https://github.com/your/repository.git`
2. Install dependencies: `composer install`
3. Copy `.env.example` to `.env` and configure your environment variables.
4. Generate application key: `php artisan key:generate`
5. Set up your database.
6. Migrate and seed the database: `php artisan migrate --seed`
7. Start the Laravel development server: `php artisan serve`

## Usage

1. Upload documents through the application.
2. Reviewers and finalizers will receive email notifications as per the pipeline steps.
3. Review and manage documents through the provided interface.

## Contributors

- [Your Name](https://github.com/yourname)
- [Contributor 2](https://github.com/contributor2)

## License

This project is licensed under the [MIT License](LICENSE).
