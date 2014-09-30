<?php  namespace Codeboard\Payments\Local;
use Codeboard\Payments\Contracts\Provider as ProviderContract;
use Illuminate\Http\Request;

abstract class AbstractProvider implements ProviderContract {

    /**
     * @var Request
     */
    protected $request;
    /**
     * @var
     */
    protected $merchantId;
    /**
     * @var
     */
    protected $portfolioId;
    /**
     * @var
     */
    protected $password;

    protected $modus;

    protected $errors = [];

    function __construct(Request $request, $merchantId, $portfolioId, $password, $modus)
    {
        $this->request = $request;
        $this->merchantId = $merchantId;
        $this->portfolioId = $portfolioId;
        $this->password = $password;
        $this->modus = $modus;
    }

    public function getErrors()
    {
        return $this->errors;
    }

}