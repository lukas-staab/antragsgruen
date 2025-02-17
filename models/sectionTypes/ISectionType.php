<?php

namespace app\models\sectionTypes;

use app\components\latex\Content;
use app\models\settings\MotionSection;
use app\models\db\{Consultation, IMotionSection, Motion};
use app\models\exceptions\FormError;
use app\models\forms\CommentForm;
use app\views\pdfLayouts\{IPDFLayout, IPdfWriter};
use CatoTH\HTML2OpenDocument\Text;
use yii\helpers\Html;

abstract class ISectionType
{
    // Synchronize with MotionTypeEdit.ts
    const TYPE_TITLE           = 0;
    const TYPE_TEXT_SIMPLE     = 1;
    const TYPE_TEXT_HTML       = 2;
    const TYPE_IMAGE           = 3;
    const TYPE_TABULAR         = 4;
    const TYPE_PDF_ATTACHMENT  = 5;
    const TYPE_PDF_ALTERNATIVE = 6;
    const TYPE_VIDEO_EMBED     = 7;

    const TYPE_API_TITLE = 'Title';
    const TYPE_API_TEXT_SIMPLE = 'TextSimple';
    const TYPE_API_TEXT_HTML = 'TextHTML';
    const TYPE_API_IMAGE = 'Image';
    const TYPE_API_TABULAR = 'TabularData';
    const TYPE_API_PDF_ATTACHMENT = 'PDFAttachment';
    const TYPE_API_PDF_ALTERNATIVE = 'PDFAlternative';
    const TYPE_API_VIDEO_EMBED = 'VideoEmbed';

    protected IMotionSection $section;
    protected bool $absolutizeLinks = false;
    protected ?Motion $motionContext = null;

    public function __construct(IMotionSection $section)
    {
        $this->section = $section;
    }

    /**
     * @return string[]
     */
    public static function getTypes(): array
    {
        return [
            static::TYPE_TITLE           => \Yii::t('structure', 'section_title'),
            static::TYPE_TEXT_SIMPLE     => \Yii::t('structure', 'section_text'),
            static::TYPE_TEXT_HTML       => \Yii::t('structure', 'section_html'),
            static::TYPE_IMAGE           => \Yii::t('structure', 'section_image'),
            static::TYPE_TABULAR         => \Yii::t('structure', 'section_tabular'),
            static::TYPE_PDF_ATTACHMENT  => \Yii::t('structure', 'section_pdf_attachment'),
            static::TYPE_PDF_ALTERNATIVE => \Yii::t('structure', 'section_pdf_alternative'),
            static::TYPE_VIDEO_EMBED     => \Yii::t('structure', 'section_video_embed'),
        ];
    }

    public static function typeIdToApi(int $type): string
    {
        switch ($type) {
            case static::TYPE_TITLE:
                return static::TYPE_API_TITLE;
            case static::TYPE_TEXT_SIMPLE:
                return static::TYPE_API_TEXT_SIMPLE;
            case static::TYPE_TEXT_HTML:
                return static::TYPE_API_TEXT_HTML;
            case static::TYPE_IMAGE:
                return static::TYPE_API_IMAGE;
            case static::TYPE_TABULAR:
                return static::TYPE_API_TABULAR;
            case static::TYPE_API_PDF_ALTERNATIVE:
                return static::TYPE_API_PDF_ALTERNATIVE;
            case static::TYPE_API_PDF_ATTACHMENT:
                return static::TYPE_API_PDF_ATTACHMENT;
            case static::TYPE_VIDEO_EMBED:
                return static::TYPE_API_VIDEO_EMBED;
            default:
                return 'Unknown';
        }
    }

    public function setAbsolutizeLinks(bool $absolutize): void
    {
        $this->absolutizeLinks = $absolutize;
    }

    // This sets the motion in whose Context an amendment will be shown. This is relevant if the proposed procedure of an amendment
    // suggests replacing this amendment by one to another motion.
    public function setMotionContext(?Motion $motion): void
    {
        $this->motionContext = $motion;
    }


    protected function getFormLabel(): string
    {
        $type = $this->section->getSettings();
        $str  = '<label for="sections_' . $type->id . '"';
        if ($type->required) {
            $str .= ' class="required" data-required-str="' . Html::encode(\Yii::t('motion', 'field_required')) . '"';
        } else {
            $str .= ' class="optional" data-optional-str="' . Html::encode(\Yii::t('motion', 'field_optional')) . '"';
        }
        $str .= '>' . Html::encode($type->title) . '</label>';

        if ($type->getSettingsObj()->public === MotionSection::PUBLIC_NO) {
            $str .= '<div class="alert alert-info"><p>' . \Yii::t('motion', 'field_unpublic') . '</p></div>';
        }

        return $str;
    }

    abstract public function isEmpty(): bool;

    abstract public function isFileUploadType(): bool;

    abstract public function getMotionFormField(): string;

    abstract public function getAmendmentFormField(): string;

    /**
     * @param string $data
     * @throws FormError
     */
    abstract public function setMotionData($data): void;

    abstract public function deleteMotionData(): void;

    /**
     * @param array $data
     * @throws FormError
     */
    abstract public function setAmendmentData($data): void;

    abstract public function getSimple(bool $isRight, bool $showAlways = false): string;

    public function getMotionPlainHtml(): string
    {
        return $this->getSimple(false);
    }

    public function getMotionEmailHtml(): string
    {
        return $this->getSimple(false);
    }

    public function getAmendmentPlainHtml(): string
    {
        return $this->getSimple(false);
    }

    abstract public function getAmendmentFormatted(string $sectionTitlePrefix = '', string $htmlIdPrefix = ''): string;

    abstract public function printMotionToPDF(IPDFLayout $pdfLayout, IPdfWriter $pdf): void;

    abstract public function printAmendmentToPDF(IPDFLayout $pdfLayout, IPdfWriter $pdf): void;

    abstract public function printMotionTeX(bool $isRight, Content $content, Consultation $consultation): void;

    abstract public function printAmendmentTeX(bool $isRight, Content $content): void;

    abstract public function getMotionODS(): string;

    abstract public function getAmendmentODS(): string;

    abstract public function printMotionToODT(Text $odt): void;

    abstract public function printAmendmentToODT(Text $odt): void;

    abstract public function getMotionPlainText(): string;

    abstract public function getAmendmentPlainText(): string;

    /**
     * @param int[] $openedComments
     */
    public function showMotionView(?CommentForm $commentForm, array $openedComments): string
    {
        return $this->getSimple(false);
    }

    abstract public function matchesFulltextSearch(string $text): bool;
}
