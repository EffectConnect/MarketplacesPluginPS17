<?php
    /**
     * @var \EffectConnect\PHPSdk\ApiCall $apiCall
     */

    if (!$apiCall->isSuccess())
    {
        echo '<h1>Invalid API call<h1><h2>The following errors have occurred:</h2><ul>';
        foreach ($apiCall->getErrors() as $curlError)
        {
            echo sprintf('<li>%s</li>', $curlError);
        }
        echo '</ul>';
        die();
    }
    $response = $apiCall->getResponseContainer();
    echo "<pre><h1>Call successful:</h1>";
    echo <<<HTML
<h2>Request details:</h2>
<ul>
    <li>Request type: {$response->getRequest()->getRequestType()}</li>
    <li>Request action: {$response->getRequest()->getRequestAction()}</li>
    <li>Request version: {$response->getRequest()->getRequestVersion()}</li>
    <li>Request identifier: {$response->getRequest()->getRequestIdentifier()}</li>
    <li>Processed at: {$response->getRequest()->getProcessedAt()->format("Y-m-d\TH:i:sP")}</li>
</ul>
HTML;
    switch ($response->getResponse()->getResult())
    {
        case \EffectConnect\PHPSdk\Core\Model\Response\Response::STATUS_SUCCESS:
            echo "<h2>The response was successful</h2>";
            print_r($response->getResponse()->getResponseContainer());
            break;
        case \EffectConnect\PHPSdk\Core\Model\Response\Response::STATUS_FAILURE:
            echo "<h2>The response failed</h2><ul>";
            foreach ($response->getErrorContainer()->getErrorMessages() as $errorMessage)
            {
                echo vsprintf("<li>%s. Code: %s. Message: %s</li>", [
                    $errorMessage->getSeverity(),
                    $errorMessage->getCode(),
                    $errorMessage->getMessage()
                ]);
            }
            echo "</ul>";
            break;
        case \EffectConnect\PHPSdk\Core\Model\Response\Response::STATUS_WARNING:
            echo "<h2>The response was successful but generated warnings</h2>";
            print_r($response->getResponse()->getResponseContainer());
            echo "<h3>Errors:</h3><ul>";
            foreach ($response->getErrorContainer()->getErrorMessages() as $errorMessage)
            {
                echo vsprintf("<li>%s. Code: %s. Message: %s</li>", [
                    $errorMessage->getSeverity(),
                    $errorMessage->getCode(),
                    $errorMessage->getMessage()
                ]);
            }
            echo "</ul>";
            break;
    }
    die();