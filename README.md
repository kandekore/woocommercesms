# WP Woo SMS

The **WP Woo SMS** plugin is a powerful tool that sends an SMS message to customers when their WooCommerce order status changes. This helps improve customer communication and order transparency.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Plugin Description](#plugin-description)
- [Installation](#installation)
- [Features](#features)
- [Usage](#usage)
  - [Enabling Email to SMS](#enabling-email-to-sms)
  - [Configuring the Plugin](#configuring-the-plugin)
  - [Auto SMS On Order Status Change](#auto-sms-on-order-status-change)
- [Admin Dashboard](#admin-dashboard)
- [Hooks and Actions](#hooks-and-actions)
- [Support](#support)
- [Author](#author)
- [Contributing](#contributing)
- [License](#license)
- [Changelog](#changelog)

## Prerequisites

Before using the WP Woo SMS plugin, make sure you have the following:

- A WordPress installation with the WooCommerce plugin installed and activated.
- An active Textlocal subscription. 

## Plugin Description

WP Woo SMS enhances your WooCommerce store's communication by automatically sending an SMS to customers each time their order status changes. The message content is customizable, allowing for a personal touch with each message sent.

## Installation

1. Download the plugin files from the GitHub repository.
2. Navigate to your WordPress Dashboard.
3. Click on 'Plugins' > 'Add New' > 'Upload Plugin'.
4. Choose the downloaded plugin files and upload them.
5. Click 'Install Now'.
6. After the plugin has been installed, click 'Activate Plugin' to start using it.

## Features

### Admin Settings

- **Email Subject**: Configure the SMS headline.
- **Email Message**: Customize the SMS message content. Use placeholders `{first_name}`, `{last_name}`, and `{order_id}` which will be replaced with actual order data.

### Auto SMS on Order Status Change

The plugin sends an SMS to the customer's phone number each time an order status changes. The SMS content is defined by the 'Email Subject' and 'Email Message' settings.

## Usage

### Enabling Email to SMS

Before you can use this plugin, you must enable Email to SMS on your Textlocal account. Follow these steps:

1. Log in to your Textlocal account.
2. Click on 'Settings', then 'Email to SMS'.
3. Check the 'Activate Email 2 SMS' tickbox.
4. Add the admin email that would send the WooCommerce emails on the right-hand side of the Email to SMS settings window.

### Configuring the Plugin

1. Navigate to 'Order Message' on your WordPress dashboard.
2. Click on 'General Settings'.
3. Configure your desired 'Email Subject' and 'Email Message' fields.

### Auto SMS On Order Status Change

Once the plugin is configured, it will automatically send an SMS to the customer's phone number each time the order status changes. 

## Admin Dashboard

The admin dashboard of the WP Woo SMS plugin provides an intuitive interface to navigate through various settings for managing your WooCommerce store's SMS notifications.

## Hooks and Actions

The WP Woo SMS plugin utilises several hooks and actions for extending the plugin's functionality:

- `woocommerce_order_status_completed`: Triggers the SMS function when an order is completed.
- `woocommerce_order_status_on-hold`: Triggers the SMS function when an order is on hold.
- `woocommerce_order_status_cancelled`: Triggers the SMS function when an order is cancelled.
- `woocommerce_order_status_pending_payment`: Triggers the SMS function when an order is pending payment.

## Support

For any inquiries or support requests, please contact [darren@kandekore.net](mailto:darren@kandekore.net).

## Author

[Darren Kandekore](https://github.com/kandekore)

## Contributing

Please see the [CONTRIBUTING.md](CONTRIBUTING.md) for details on how you can contribute to the development of WP Woo SMS.

## License

This plugin is released under the GPLv2 or later license. See the [LICENSE](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html) file for more details.

## Changelog

### Version 1.0
- Initial release
