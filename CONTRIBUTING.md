# Contribution Guide

Thank you for your interest in contributing to **msamgan/laravel-env-keys-checker**! We welcome contributions of all kinds—whether you're fixing bugs, adding features, improving documentation, or just reporting an issue. This guide will help you get started and ensure that your contributions are aligned with our goals and standards.

## How to Contribute

### 1. Reporting Issues
If you've found a bug, have a feature request, or want to report a security vulnerability, we encourage you to submit an issue:

- **Bug Reports**: Provide a detailed description of the problem, including the steps to reproduce, the expected behavior, and screenshots/logs if possible.
- **Feature Requests**: Explain the use case for the feature, and how it will benefit users. If possible, include ideas for implementation.
- **Security Issues**: For sensitive security vulnerabilities, please email us directly at `[mail@msamgan.com]`.

### 2. Fork the Repository
To contribute code, follow these steps:

1. **Fork the repository** by clicking the "Fork" button in the top-right corner of the GitHub page.
2. **Clone the fork** to your local machine:
   ```bash
   git clone https://github.com/your-username/laravel-env-keys-checker.git
   cd laravel-env-keys-checker
   ```
3. **Create a new branch** for your changes:
   ```bash
   git checkout -b my-branch-name
   ```

### 3. Making Changes
- Follow the [Coding Standards](#coding-standards) when writing code.
- Write clear and concise **commit messages**.
- Include **unit tests** for any new features or bug fixes if you can.
- **Run tests locally** before pushing changes:
  ```bash
  ./vendor/bin/pest
  ```

### 4. Submitting a Pull Request
Once your changes are ready, submit a pull request (PR):

1. Push your branch to your fork:
   ```bash
   git push origin my-branch-name
   ```
2. Open a pull request to the `next release version` branch of the original repository.
3. Provide a **clear description** of your changes, explaining why the change is needed and what it does.
4. The project maintainers will review your PR, suggest any necessary changes, and merge it when it's ready.

### 5. Code Review
Please be prepared to make changes based on the review. We want to ensure that all contributions meet the project's standards for quality and maintainability.
