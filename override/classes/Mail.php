<?php

/**
 * Class Mail
 */
class Mail extends MailCore
{
    /**
     * TODO: has this function declaration been the same for all Prestashop versions since 1.7?
     *
     * @param int $idLang
     * @param string $template
     * @param string $subject
     * @param string $templateVars
     * @param string $to
     * @param null $toName
     * @param null $from
     * @param null $fromName
     * @param null $fileAttachment
     * @param null $mode_smtp
     * @param string $templatePath
     * @param false $die
     * @param null $idShop
     * @param null $bcc
     * @param null $replyTo
     * @param null $replyToName
     * @return bool|int
     */
    public static function send(
        $idLang,
        $template,
        $subject,
        $templateVars,
        $to,
        $toName = null,
        $from = null,
        $fromName = null,
        $fileAttachment = null,
        $mode_smtp = null,
        $templatePath = _PS_MAIL_DIR_,
        $die = false,
        $idShop = null,
        $bcc = null,
        $replyTo = null,
        $replyToName = null
    ) {
        if (is_array($templateVars) && isset($templateVars['ec_send_mail']) && $templateVars['ec_send_mail'] === false) {
            return true;
        }

        return parent::send(
            $idLang,
            $template,
            $subject,
            $templateVars,
            $to,
            $toName,
            $from,
            $fromName,
            $fileAttachment,
            $mode_smtp,
            $templatePath,
            $die,
            $idShop,
            $bcc,
            $replyTo,
            $replyToName
        );
    }
}
