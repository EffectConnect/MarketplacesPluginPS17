<?php

    namespace EffectConnect\PHPSdk\Core\Model\Request;

    use EffectConnect\PHPSdk\Core\Abstracts\ApiModel;
    use EffectConnect\PHPSdk\Core\Interfaces\ApiModelInterface;

    final class ChannelListRequest extends ApiModel implements ApiModelInterface
    {

        public function getName()
        {
            return 'channellist';
        }

    }
