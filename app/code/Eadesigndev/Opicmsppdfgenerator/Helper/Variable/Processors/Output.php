<?php
/**
 * Created by PhpStorm.
 * User: eadesignpc
 * Date: 10/23/2016
 * Time: 8:45 AM
 */

namespace Eadesigndev\Opicmsppdfgenerator\Helper\Variable\Processors;


class Output extends Pdf
{

    /**
     * @var array
     */
    private $PDFFiles = [];

    /**
     * @param $parts
     * @return string
     */
    protected function _eaPDFSettings($parts)
    {

        $templateModel = $this->template;

        if (!$templateModel->getTemplateCustomForm()) {
            $pdf = new \mPDF(
                $mode = '',
                $format = $this->paperFormat(
                    $templateModel->getTemplatePaperForm(),
                    $templateModel->getTemplatePaperOri()
                ),
                $default_font_size = 0,
                $default_font = '',
                $mgl = $templateModel->getTemplateCustomL(),
                $mgr = $templateModel->getTemplateCustomR(),
                $mgt = $templateModel->getTemplateCustomT(),
                $mgb = $templateModel->getTemplateCustomB(),
                $mgh = 9,
                $mgf = 9
            );
        }

        if ($templateModel->getTemplateCustomForm()) {
            $pdf = new \mPDF(
                '',
                [
                    $templateModel->getTemplateCustomW(),
                    $templateModel->getTemplateCustomH()
                ],
                $default_font_size = 0,
                $default_font = '',
                $mgl = $templateModel->getTemplateCustomL(),
                $mgr = $templateModel->getTemplateCustomR(),
                $mgt = $templateModel->getTemplateCustomT(),
                $mgb = $templateModel->getTemplateCustomB(),
                $mgh = 9,
                $mgf = 9
            );
        }

        //todo check for header template processing problem width breaking the templates.
        $pdf->SetHTMLHeader(html_entity_decode($parts['header']));
        $pdf->SetHTMLFooter(html_entity_decode($parts['footer']));

        $pdf->WriteHTML($templateModel->getTemplateCss(), 1);

        $pdf->WriteHTML('<body>' . html_entity_decode($parts['body']) . '</body>');

        $tmpFile = $this->directoryList->getPath('tmp') .
            DIRECTORY_SEPARATOR .
            $this->source->getIncrementId() .
            '.pdf';

        $this->PDFFiles[] = $tmpFile;

        $pdf->Output($tmpFile, 'F');
    }

    /**
     * @return string
     */
    public function PDFmerger($templateModel = false)
    {

        $files = $this->PDFFiles;
        $model = $this->template;

        if (!$templateModel) {
            $templateModel = $model;
        }

        if (!$templateModel->getTemplateCustomForm()) {

            $ori = $templateModel->getTemplatePaperOri();

            $arrayOri = explode('-', $ori);
            if (count($arrayOri) > 1) {
                $finalOri = $arrayOri[1];
            }

            $pdf = new \mPDF(
                $mode = '',
                $format = $this->paperFormat(
                    $templateModel->getTemplatePaperForm(),
                    $templateModel->getTemplatePaperOri()
                ),
                $default_font_size = 0,
                $default_font = '',
                $mgl = $templateModel->getTemplateCustomL(),
                $mgr = $templateModel->getTemplateCustomR(),
                $mgt = $templateModel->getTemplateCustomT(),
                $mgb = $templateModel->getTemplateCustomB(),
                $mgh = 9,
                $mgf = 9,
                $finalOri
            );
        }

        if ($templateModel->getTemplateCustomForm()) {
            $pdf = new \mPDF(
                '',
                [
                    $templateModel->getTemplateCustomW(),
                    $templateModel->getTemplateCustomH()
                ],
                $default_font_size = 0,
                $default_font = '',
                $mgl = $templateModel->getTemplateCustomL(),
                $mgr = $templateModel->getTemplateCustomR(),
                $mgt = $templateModel->getTemplateCustomT(),
                $mgb = $templateModel->getTemplateCustomB(),
                $mgh = 9,
                $mgf = 9
            );
        }

        $filesTotal = sizeof($files);
        $fileNumber = 1;

        $pdf->SetImportUse();

        foreach ($files as $fileName) {
            if ($this->file->isExists($fileName)) {
                $pagesInFile = $pdf->SetSourceFile($fileName);
                for ($i = 1; $i <= $pagesInFile; $i++) {
                    $tplId = $pdf->ImportPage($i);
                    $pdf->UseTemplate($tplId);
                    if (($fileNumber < $filesTotal) || ($i != $pagesInFile)) {
                        $pdf->WriteHTML('<pagebreak />');
                    }
                }
            }
            $fileNumber++;
        }

        $pdfToOutput = $pdf->Output('', 'S');

        foreach ($files as $fileName) {
            $this->file->deleteFile($fileName);
        }

        return $pdfToOutput;
    }

}