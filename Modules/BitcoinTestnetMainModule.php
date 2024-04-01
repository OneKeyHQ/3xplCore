<?php declare(strict_types = 1);

final class BitcoinTestnetMainModule extends UTXOMainModule implements Module
{
    function initialize()
    {
        // CoreModule
        $this->blockchain = 'bitcoin';
        $this->module = 'bitcoin-testnet-main';
        $this->is_main = true;
        $this->currency = 'bitcoin'; // Static
        $this->currency_details = ['name' => 'Bitcoin', 'symbol' => 'BTC', 'decimals' => 8, 'description' => null];
        $this->first_block_date = '2011-02-02';

        // UTXOMainModule
        $this->p2pk_prefix1 = '1';
        $this->p2pk_prefix2 = '00';

        // Use OneAddressInScriptPubKey
        $this->extra_features = [UTXOSpecialFeatures::OneAddressInScriptPubKey];
    }
}
