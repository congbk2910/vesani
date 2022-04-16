<?php
namespace Svea\Checkout\Model\Client\DTO\Order;

class Gui
{

    /** @var $Layout string */
    protected $Layout;

    /** @var $Snippet string */
    protected $Snippet;

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->Layout;
    }

    /**
     * @param string $Layout
     * @return Gui
     */
    public function setLayout($Layout)
    {
        $this->Layout = $Layout;
        return $this;
    }

    /**
     * @return string
     */
    public function getSnippet()
    {
        return $this->Snippet;
    }

    /**
     * @param string $Snippet
     * @return Gui
     */
    public function setSnippet($Snippet)
    {
        $this->Snippet = $Snippet;
        return $this;
    }
    
}