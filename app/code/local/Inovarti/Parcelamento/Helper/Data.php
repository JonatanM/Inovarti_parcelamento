<?php

/**
 *
 * @category   Inovarti
 * @package    Inovarti_Parcelamento
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Parcelamento_Helper_Data extends Mage_Core_Helper_Abstract {

    public function isEnable() {
        return Mage::getStoreConfig('payment/parcelamento/enabled');
    }

    public function MostraParcelaProduct($_product) {


        if ($this->isEnable()) {
            $html = '';

            $_coreHelper = Mage::helper('core');

            $_price = $_product->getPrice();
            $_finalPrice = $_product->getFinalPrice();


            $numero_de_parcelas = Mage::getStoreConfig('payment/parcelamento/parcelamento');
            $valor_parcelas = Mage::getStoreConfig('payment/parcelamento/price_min');
            $_desconto_parcelamento = Mage::getStoreConfig('payment/parcelamento/desconto_parcelamento');

            $valor = ($_finalPrice == $_price) ? $_price : $_finalPrice;

            if ($valor > $valor_parcelas) {
                for ($counter = $numero_de_parcelas; $counter >= 1; $counter--) {
                    if ($valor / $counter >= $valor_parcelas) {
                        $valor_parcelas = $valor / $counter;
                        $numero_de_parcelas = $counter;
                        break;
                    }
                }
                if ($valor == $valor_parcelas)
                    $valor_parcelas = ($_finalPrice == $_price) ? number_format($_price, 2) : number_format($_finalPrice, 2);
            }else {
                $valor_parcelas = ($_finalPrice == $_price) ? $_price : $_finalPrice;
                $numero_de_parcelas = 1;
            }

            $final_valor_parcelas = $_coreHelper->currency($valor_parcelas, true, false);

            $boleto = $valor - ($valor * $_desconto_parcelamento / 100);


            $html .= '<div class="price-box parcelado">';

            if ($_finalPrice > $_price):
                $html .= '(economia de ' . $_coreHelper->currency($_finalPrice - $_price, true, true) . ')';
            endif;

            if ($numero_de_parcelas > 1):
                $html .= '<div class="price-box parcelado">em ' . $numero_de_parcelas . 'x de ' . $final_valor_parcelas . ' sem juros<br>';
            endif;


            if ($boleto > 1) {
                $html .= 'ou <strong>' . $_coreHelper->currency($boleto, true, true) . '</strong> à vista com desconto</div>';
            }
            $html .= '</div>';
        }

        return $html;
    }

}