<?php
namespace AppBundle\Service\Common\Session;

use Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy;

class EncryptedSessionProxy extends SessionHandlerProxy
{
    private $key;

    public function __construct(\SessionHandlerInterface $handler, $key)
    {
        $this->key = $key;

        parent::__construct($handler);
    }

    public function read($id)
    {
        $data = parent::read($id);

        return mcrypt_decrypt(\MCRYPT_3DES, $this->key, $data);
    }

    public function write($id, $data)
    {
        $data = mcrypt_encrypt(\MCRYPT_3DES, $this->key, $data);

        return parent::write($id, $data);
    }
}
