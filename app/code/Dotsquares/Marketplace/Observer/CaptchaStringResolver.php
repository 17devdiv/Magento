<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Observer;
/**
 * This class contains functions for captcha validation
 * @author user
 *
 */
class CaptchaStringResolver {
    /**
     * Get Captcha String
     *
     * @param \Magento\Framework\App\RequestInterface $request            
     * @param string $formId            
     * @return string
     */
    public function resolve(\Magento\Framework\App\RequestInterface $request, $formId) {
        /**
         * Assign captcha params
         */
        $captchaParams = $request->getPost ( \Magento\Captcha\Helper\Data::INPUT_NAME_FIELD_VALUE );
        
        /**
         * Return captcha string
         */
        return isset ( $captchaParams [$formId] ) ? $captchaParams [$formId] : '';
    }
}