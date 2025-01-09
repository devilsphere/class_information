# REDCap Class Information Module

## Overview

The **REDCap Class Information Module** is a custom tool designed to reflect on and display details about available PHP classes within the REDCap environment. It generates a detailed list of methods for the selected class, including their visibility, return types, parameters, and other metadata. The tool provides options to download this information as either an HTML or PDF file for further use.

This module is accessible through a custom link in the REDCap Control Panel, ensuring that only users with administrative privileges can access it.

---

## Features

- **Class Selection:** Provides a dropdown menu of all available PHP classes for inspection.
- **Detailed Method Information:**
    - Visibility (`public`, `protected`, or `private`).
    - Static, abstract, and final properties.
    - Declaring class.
    - Return type.
    - Parameter details, including type hints, default values, and variadic or reference status.
    - Doc comments (if available).
- **Export Options:**
    - Download the information as an HTML file.
    - Download the information as a PDF file using FPDF.
- **Restricted Access:** Only administrators can access this page via the REDCap Control Panel.

---


## How to Use

1. **Navigate to the Class Inspector:**
    - Log in as an administrator and access the **Check Classes** link in the REDCap Control Panel.

2. **Select a Class:**
    - From the dropdown menu, select the class you want to inspect.

3. **View Class Information:**
    - The page displays all methods in the selected class, including:
        - Method name.
        - Visibility and properties (e.g., static, abstract).
        - Declaring class and return type.
        - Detailed parameter information.

4. **Download the Information:**
    - Use the provided buttons to download the class information as:
        - An **HTML** file.
        - A **PDF** file.

---

## Security

- **Restricted Access:** Only users with administrative privileges can access the **Class Inspector** module.
- **No External Access:** The tool is accessible only from the REDCap Control Panel link, ensuring it is not exposed to unauthorized users.
- **Data Sanitization:** The module uses `htmlspecialchars` to sanitize all dynamic outputs, protecting against injection attacks.

---

## Troubleshooting

### Common Issues

1. **PDF Not Generating:**
    - Ensure the `fpdf.php` file is included in the `ClassInspectorModule` directory.
    - Verify that the `fpdf.php` file is accessible and readable by the server.

---


## License

This module is released under the MIT License. See the LICENSE file for details.

---
