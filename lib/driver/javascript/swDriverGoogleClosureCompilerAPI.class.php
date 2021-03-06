<?php
/**
 * npAssetsOptimizerPlugin Google Closure Compiler API javascript optimization driver
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  driver
 * @author      Nicolas Perriault <nperriault@gmail.com>
 *
 * @see http://code.google.com/intl/en_US/closure/compiler/docs/api-ref.html
 */
class swDriverGoogleClosureCompilerApi extends swDriverBase
{
  const SERVICE_URL = 'http://closure-compiler.appspot.com/compile';
  
  public function doProcessFile($file, $replace = false)
  {
    if (!function_exists('curl_init'))
    {
      throw new RuntimeException('PHP CURL support must be enabled to use the Google Closure Compiler API driver');
    }
    
    $content = file_get_contents($file);
    
    $ch = curl_init(self::SERVICE_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'output_info=compiled_code&output_format=text&compilation_level=SIMPLE_OPTIMIZATIONS&js_code='.urlencode($content));
    
    $optimizedContent = curl_exec($ch);
    
    if (200 != ($httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE)))
    {
      throw new RuntimeException(sprintf('The Google Closure Compiler API returned an HTTP %d error: %s', $httpCode, $optimizedContent));
    }
    
    curl_close($ch);
    
    if ($replace)
    {
      return parent::replaceFile($file, $optimizedContent);
    }
    else
    {
      return $optimizedContent;
    }
  }
}
