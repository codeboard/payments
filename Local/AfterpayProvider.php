<?php  namespace Codeboard\Payments\Local;

use Codeboard\Payments\PaymentException;
use Illuminate\Support\Collection;

class AfterpayProvider extends AbstractProvider {

    protected $afterpay;

    public function order($requestData, $type, $orderLine)
    {
        $afterpay = new Afterpay;
        $afterpay->create_order_line( $orderLine['sku'], $orderLine['name'], $orderLine['qty'], $orderLine['price'], $orderLine['tax_category']);
        $afterpay->set_order( $requestData, $type);

        $data = [
            'merchantid' => $this->merchantId,
            'portfolioid' => $this->portfolioId,
            'password' => $this->password
        ];
        $afterpay->validate_and_check_order($data, $this->modus);
        $this->findErrors($afterpay);
        $this->afterpay = $afterpay;
        return $this;
    }

    public function result()
    {
        if( count( $this->errors) )
            throw new PaymentException(implode(', ', $this->getErrors()));
        return $this->mapResults((array) $this->afterpay->order_result->return);
    }

    public function findErrors($data)
    {
        if( isset( $data->order_result->return->failures ) )
            $this->errors = $this->mapErrorsToArray($data->order_result->return->failures);
        if( isset( $data->order_result->return->rejectDescription) )
            $this->errors[] = $data->order_result->return->rejectDescription;
    }

    public function mapErrorsToArray($items)
    {
        return array_map( function ($item) {
            return $item->fieldname;
        }, $items);
    }

    public function mapResults($item)
    {
        return [
            'status' => $item['statusCode'],
            'result' => $item['resultId'],
            'order_reference' => $item['afterPayOrderReference'],
            'transaction_id' => $item['transactionId']
        ];
    }

}