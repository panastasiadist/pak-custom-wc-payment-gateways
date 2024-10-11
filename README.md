# PAK Custom Payment Gateways for WooCommerce

A plugin for WordPress which enables developers to easily add simple, yet customizable payment gateways to WooCommerce.

## Usage

### 1. Installation

The plugin is freely available on the [WordPress Plugin Repository](https://wordpress.org/plugins/pak-custom-wc-payment-gateways/). You can directly search for, install, and activate it from your WordPress installation. Alternatively, you can download it from the plugin repository, upload, and activate it on your site.

### 2. How to Use

1. Log in your WordPress administration site.
2. Install the plugin.
3. Navigate to **Settings -> PAK Custom Payment Gateways for WooCommerce**.
4. Click on the **New Gateway** button to create a new custom gateway. A dialog will appear.
5. Specify the (default) title and description of the new gateway.
6. Click on the **Create** button to save the new gateway and close the dialog.
7. The new gateway should appear in the list.
8. Navigate to **WooCommerce -> Settings -> Payments**.
9. You should now see the newly added gateway.
10. Access the settings page for each gateway and configure it as necessary.
11. Alternatively, click on the **Configure** icon/button appearing within the row corresponding to the new gateway. This will directly take you to the gateway's configuration page.

## Configuration

This section outlines the configuration settings available for each gateway in the **WooCommerce -> Settings -> Payments** administration screen.

| Option                      | Description                                                                                                                                                               |
|-----------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Enable/Disable              | Check this to enable the gateway.                                                                                                                                         |
| Icon URL                    | The gateway's icon shown to customers.                                                                                                                                    |
| Title                       | The gateway's title shown to customers.                                                                                                                                   |
| Description                 | The gateway's description shown to customers on the checkout page.                                                                                                        |
| Instructions                | Payment instructions relayed to a customer on the thank-you page and through emails.                                                                                      |
| Order Status                | An order's status after it has been placed using the gateway.                                                                                                             |
| Order's Min Amount          | The minimum total cart amount required for the gateway to be available to customers.                                                                                      |
| Order's Max Amount          | The maximum total cart amount required for the gateway to be available to customers.                                                                                      |
| Enable for shipping methods | Restrict gateway's availability to specific shipping methods only. Leave it blank to make the gateway always available regardless of a customer's chosen shipping method. |
| Enable for virtual orders   | Make the gateway available for orders containing virtual products only.                                                                                                   |

## Filters

The plugin provides several WordPress filters to enable customization by developers. Some filters are available in two types: generic and gateway-specific.

- Generic filters are invoked for every custom gateway. The id of each gateway is provided as the last parameter (`$id`) to each function implementing the filter.
- Gateway-specific filters include an **[ID]** placeholder as part of the filter's name. This placeholder should be replaced with the gateway's id, as shown in the plugin's settings page.

> [!NOTE]
> When both types of filters are available, the gateway-specific filter is executed first, immediately followed by the generic one. 

> [!WARNING]
> - The filters discussed here may be invoked both on the administration and public facing site. As a result, when a filter's handler function is called, you should not make assumptions about the context. Instead, conduct proper checks if your code carries out context-sensitive operations.
> - Returning non-valid data from a filter implementation, may result in unexpected behavior, bugs, or the website completely crashing.

### Decide on a gateway's availability

#### Filters (Name => Handler Signature)

- `pak_custom_wc_payment_gateways/[ID]/is_available` => `(bool $is_available): bool`
- `pak_custom_wc_payment_gateways/is_available` => `(bool $is_available, string $id): bool`

#### Parameters

- `$is_available` - Determines whether the gateway should be available to the current customer. By default, this depends on the configuration, cart, and checkout details.
- `$id` - The id of the gateway for which the filter is being called.

#### Examples

```php
add_filter( 'pak_custom_wc_payment_gateways/[ID]/is_available', function( bool $is_available ) {
    return $is_available;
} );

// OR

add_filter( 'pak_custom_wc_payment_gateways/is_available', function( bool $is_available, string $id ) {
    return $is_available;
}, 10, 2 );
```

### Decide whether a gateway's instructions should be included in an order's email

#### Filters (Name => Handler Signature)

- `pak_custom_wc_payment_gateways/[ID]/send_email_instructions` => `(bool $send, WC_Order $order): string`
- `pak_custom_wc_payment_gateways/send_email_instructions` => `(bool $send, WC_Order $order, string $id): string`

#### Parameters

- `$send` - Whether the instructions should be sent. The provided value is true when instructions have been specified for the gateway, the email is to be sent to the customer, and the order is considered non paid based on its status.
- `$order` - The specific order for which an email is to be sent.
- `$id` - The id of the gateway for which the filter is being called.

#### Examples

```php
add_filter( 'pak_custom_wc_payment_gateways/[ID]/send_email_instructions', function( bool $send, WC_Order $order ) {
    return $send;
}, 10, 2 );

// OR

add_filter( 'pak_custom_wc_payment_gateways/send_email_instructions', function( bool $send, WC_Order $order, string $id ) {
    return $send;
}, 10, 3 );
```

### Process a gateway's settings

#### Filters (Name => Handler Signature)

- `pak_custom_wc_payment_gateways/[ID]/settings/[KEY]` => `($value): mixed`
- `pak_custom_wc_payment_gateways/settings/[KEY]` => `($value, string $id): mixed`

#### Parameters

- `$value` - The value corresponding to the setting's key. Its type depends on the key.
- `$id` - The id of the gateway for which the filter is being called.

Each gateway is characterized by multiple settings. Most of them can be dynamically adjusted through the means of filters. These settings consist of a key and a corresponding value, with the **[KEY]** portion of a filter's name meant to be substituted for a setting name:

| Key                         | Value Type       | Comments                                                                                                                                                                                                                                                                                                                               |
|-----------------------------|:-----------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| icon                        | string           | Return a URL to a publicly accessible image.                                                                                                                                                                                                                                                                                           |
| title                       | string           | -                                                                                                                                                                                                                                                                                                                                      |
| description                 | string           | -                                                                                                                                                                                                                                                                                                                                      |
| instructions                | string           | -                                                                                                                                                                                                                                                                                                                                      |
| order_placed_status         | string           | Return a value that corresponds to a supported WooCommerce order status. You might want to use or refer to the [wc_get_order_statuses()](https://woocommerce.github.io/code-reference/files/woocommerce-includes-wc-order-functions.html#function_wc_get_order_statuses) WooCommerce function to better understand these status codes. |
| order_min_amount            | integer          | -                                                                                                                                                                                                                                                                                                                                      |
| order_max_amount            | integer          | -                                                                                                                                                                                                                                                                                                                                      |
| enable_for_shipping_methods | array of strings | Return an one-dimension array of string elements, each one referring to a shipping method/rate. Each element must conform to the following formats: **shipping_method_id** or **shipping_method_id:shipping_method_instance_id**                                                                                                       |
| enable_for_virtual_orders   | string           | Return a `'yes'` or `'no'` value.                                                                                                                                                                                                                                                                                                      |

#### Examples

```php
add_filter( 'pak_custom_wc_payment_gateways/[ID]/settings/[KEY]', function( $value ) {
    return $value;
} );

// OR

add_filter( 'pak_custom_wc_payment_gateways/settings/[KEY]', function( $value, string $id ) {
    return $value;
}, 10, 2 );
```

### Process the plugin's configuration

#### Filter (Name => Handler Signature)

- `pak_custom_wc_payment_gateways/configuration` => `(Configuration $configuration): Configuration`

#### Parameters

- `$configuration` - An instance of the `panastasiadist\PAK_Custom_WC_Payment_Gateways\Models\Configuration` class accessor functions that you can use to read or modify the plugin's configuration.

#### Example

```php
add_filter( 'pak_custom_wc_payment_gateways/configuration', function( panastasiadist\PAK_Custom_WC_Payment_Gateways\Models\Configuration $config ) {
    return $config;
} );
```
