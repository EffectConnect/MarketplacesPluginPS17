<?php

namespace EffectConnect\Marketplaces\Controller;

use EffectConnect\Marketplaces\Exception\FileZipCreationFailedException;
use EffectConnect\Marketplaces\Helper\FileCleanHelper;
use EffectConnect\Marketplaces\Helper\FileDownloadHelper;
use EffectConnect\Marketplaces\Helper\FilePathInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminLogController
 * @package EffectConnect\Marketplaces\Controller
 */
class AdminLogController extends FrameworkBundleAdminController
{
    /**
     * @param Request $request
     * @return Response|null
     */
    public function indexAction(Request $request)
    {
        return $this->render('@Modules/effectconnect_marketplaces/views/templates/admin/LogController.index.html.twig', [
            'layoutTitle'        => $this->trans('Logs', 'Modules.Effectconnectmarketplaces.Admin'),
            'enableSidebar'      => true,
            'dataFolder'         => realpath(FilePathInterface::DATA_DIRECTORY),
            'fileExpirationDays' => FileCleanHelper::TMP_FILE_EXPIRATION_DAYS,
        ]);
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse|RedirectResponse
     */
    public function downloadAction(Request $request)
    {
        try {
            $zipFileName = FileDownloadHelper::downloadDataFolderZip();
        } catch (FileZipCreationFailedException $e) {
            $this->addFlash('error', $this->trans($e->getMessage(), 'Modules.Effectconnectmarketplaces.Admin'));
            return $this->redirectToRoute('effectconnect_marketplaces_admin_log_index');
        }

        $file = new File($zipFileName);
        return $this->file($file);
    }
}
