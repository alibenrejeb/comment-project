<?php
namespace App\Encoder;

use JWT\Authentication\JWT;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;

class JWTEncoder implements JWTEncoderInterface
{
    private string $secretKey;

    /**
     * Constructor class.
     *
     * @param string $secretKey
     */
    public function __construct($secretKey)
    {
        $this->secretKey = $secretKey;
    }

	/**
	 * @param array $data
	 * @return string the encoded token string
	 */
	public function encode(array $data) {
        try {
            return JWT::encode($data, $this->secretKey);
        } catch (\Exception $e) {
            throw new JWTEncodeFailureException(JWTEncodeFailureException::INVALID_CONFIG, 'An error occurred while trying to encode the JWT token.', $e);
        }
	}
	
	/**
	 *
	 * @param string $token
	 * @return array
	 */
	public function decode($token) {
        try {
            return (array) JWT::decode($token, $this->secretKey);
        } catch (\Exception $e) {
            throw new JWTDecodeFailureException(JWTDecodeFailureException::INVALID_TOKEN, 'Invalid JWT Token', $e);
        }
	}
}
