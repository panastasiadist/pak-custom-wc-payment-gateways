import Configuration from './interfaces/Configuration';
import jQuery from 'jquery';

declare global {
    interface Window {
        jQuery: jQuery,
        pak_custom_wc_payment_gateways: Configuration;
    }
}
