# Customizable Discount Module for OpenCart 3 **BETA**

## Abaut BETA version
In the beta version, in the admin panel of the module, it is possible to select only categories and their combinations **need to add manufacturer, payment, shipping to conditions array at `/admin/controller/extension/total/customizable_discount.php`**

## Overview

The Customizable Discount Module is a powerful extension for OpenCart 3 that allows store administrators to create flexible discount rules based on product categories, manufacturers, shippings or payments. This module enhances the default discount functionality in OpenCart, enabling more targeted and complex discount schemes.

## Features

- **Flexible Discount Rules:** Configure discounts based on product categories, manufacturers, shippings, payments or a combination of both.
- **Conditional Logic (AND/OR):**
  - **AND Logic:** Apply discounts only when all specified conditions are met.
  - **OR Logic:** Apply discounts if any of the specified conditions are met.
- **Priority Sorting:** Define the order in which discount rules are applied to ensure the correct rule is prioritized.
- **Admin Interface:** User-friendly interface within the OpenCart admin panel for setting up and managing discount rules.
- **Multi-Language Support:** Includes settings for discount names in different languages ​​and language files for easy translation of the module's admin panel.

## How It Works

1. **Set Up Discount Rules:** Configure discount rules in the admin panel, specifying conditions such as product categories or manufacturers.
2. **Choose Conditional Logic:** Decide whether the discount should apply using "AND" logic (all conditions must be met) or "OR" logic (any condition can trigger the discount).
3. **Apply Discounts at Checkout:** The module automatically applies the appropriate discount during the checkout process based on the configured rules and logic.

## Installation

1. Upload the module files to your OpenCart 3.
2. Go to the OpenCart admin panel, navigate to Extensions > Extensions, and select "Order Totals."
3. Install and configure the "Customizable Discount" module.