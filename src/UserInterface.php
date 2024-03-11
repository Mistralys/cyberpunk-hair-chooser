<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

use AppUtils\Interfaces\StringableInterface;
use Mistralys\CPHairChooser\UI\Page;
use function AppLocalize\t;
use function AppUtils\sb;

class UserInterface
{
    public const SESSION_PREFIX = 'cphairchooser_';
    public const SESSION_MESSAGES = 'messages';

    public const MESSAGE_TYPE_INFO = 'info';
    public const MESSAGE_TYPE_SUCCESS = 'success';
    public const MESSAGE_TYPE_ERROR = 'error';
    public const MESSAGE_TYPE_WARNING = 'warning';
    public const PAGE_NUMBERS = 'numbers';
    public const PAGE_DOWNLOADS = 'downloads';
    public const PAGE_EXTRACT = 'extract';
    public const PAGE_ADD_MOD = 'add-mod';
    public const PAGE_EDIT_MOD = 'edit-mod';
    public const PAGE_BUILD_MOD = 'build-mod';
    public const PAGE_DELETE_MOD = 'delete-mod';
    public const PAGE_MODS_LIST = 'mods-list';
    public const PAGE_MEDIA_VIEWER = 'media-viewer';

    private static ?UserInterface $instance = null;

    /**
     * @var array<string,Page>
     */
    private array $pages = array();

    public function __construct()
    {
        $this->addPage(self::PAGE_DOWNLOADS, t('Selected downloads'), __DIR__.'/../pages/downloads-list.php')
            ->setTitle(t('Downloaded mods selection'))
            ->setAbstract(sb()
                ->t('These are the available downloaded mods.')
                ->t('Select the ones that contain hair archives, to be able to use them.')
            );

        $this->addPage(self::PAGE_EXTRACT, t('Extract files'), __DIR__.'/../pages/extract-files.php')
            ->setTitle(t('Extract mod files'))
            ->setAbstract(sb()
                ->t('These are all the hair mods that were selected from the downloads list.')
                ->t('They must be extracted to be used in mod configurations.')
            );

        $this->addPage(self::PAGE_NUMBERS, t('Hair archives'), __DIR__.'/../pages/numbers.php')
            ->setTitle(t('Available hair archive files'))
            ->setAbstract(t('These are all hair archive files that were found in the extracted mod folders.'));

        $this->addPage(self::PAGE_MODS_LIST, t('Mods'), __DIR__.'/../pages/mods-list.php')
            ->setTitle(t('Available mod configurations'))
            ->setAbstract(sb()
                ->t('These are all the mod configurations that have been created.')
                ->t('Each can can be saved to a mod ZIP file with the %1$s link.', sb()->quote(t('Build')))
            );

        $this->addPage(self::PAGE_ADD_MOD, t('Add mod'), __DIR__.'/../pages/mod-details.php')
            ->setTitle(t('Add a mod configuration'));

        $this->addPage(self::PAGE_EDIT_MOD, t('Edit mod'), __DIR__.'/../pages/mod-details.php')
            ->setInNav(false);

        $this->addPage(self::PAGE_BUILD_MOD, t('Build mod'), __DIR__.'/../pages/mod-build.php')
            ->setAbstract(t('This is where a mod configuration can be built into a mod file.'))
            ->setInNav(false);

        $this->addPage(self::PAGE_DELETE_MOD, t('Delete mod'), __DIR__.'/../pages/mod-delete.php')
            ->setInNav(false);

        $this->addPage(self::PAGE_MEDIA_VIEWER, t('Media viewer'), __DIR__.'/../pages/media-viewer.php')
            ->setInNav(false);
    }

    public static function getInstance() : UserInterface
    {
        if(is_null(self::$instance)) {
            self::$instance = new UserInterface();
        }

        return self::$instance;
    }

    /**
     * @return array<int,array{message:string,type:string}>
     */
    public function getMessages() : array
    {
        $messages = $this->getSessionVar(self::SESSION_MESSAGES);
        if(is_array($messages)) {
            return $messages;
        }

        return array();
    }

    public function addInfoMessage($message) : self
    {
        return $this->addMessage($message, self::MESSAGE_TYPE_INFO);
    }

    public function addSuccessMessage($message) : self
    {
        return $this->addMessage($message, self::MESSAGE_TYPE_SUCCESS);
    }

    public function addWarningMessage($message) : self
    {
        return $this->addMessage($message, self::MESSAGE_TYPE_WARNING);
    }

    public function addErrorMessage($message) : self
    {
        return $this->addMessage($message, self::MESSAGE_TYPE_ERROR);
    }

    /**
     * @param string|int|float|StringableInterface|NULL $message
     * @param string $type
     * @return $this
     */
    private function addMessage(StringableInterface|string|int|float|null $message, string $type) : self
    {
        if(empty($message)) {
            return $this;
        }

        $messages = $this->getMessages();

        $messages[] = array(
            'type' => $type,
            'message' => (string)$message
        );

        $this->setSessionVar(self::SESSION_MESSAGES, $messages);

        return $this;
    }

    public function getSessionVar(string $name): mixed
    {
        return $_SESSION[self::SESSION_PREFIX.$name] ?? null;
    }

    public function setSessionVar(string $name, mixed $value) : self
    {
        $_SESSION[self::SESSION_PREFIX.$name] = $value;
        return $this;
    }

    public function clearMessages() : self
    {
        $this->setSessionVar(self::SESSION_MESSAGES, array());
        return $this;
    }

    public function addPage(string $id, string $label, string $includeFile) : Page
    {
        $page = new Page($id, $label, $includeFile);
        $this->pages[$id] = $page;
        return $page;
    }

    /**
     * @return Page[]
     */
    public function getPages(): array
    {
        return array_values($this->pages);
    }

    /**
     * @param string $id
     * @return Page
     * @throws HairChooserException
     */
    public function getPageByID(string $id) : Page
    {
        if(isset($this->pages[$id])) {
            return $this->pages[$id];
        }

        throw new HairChooserException(
            'Page not found',
            sprintf(
                'The page with the ID [%s] does not exist.',
                $id
            )
        );
    }

    public function getActivePageID() : string
    {
        if(isset($_REQUEST['page'], $this->pages[$_REQUEST['page']])) {
            return $_REQUEST['page'];
        }

        return self::PAGE_NUMBERS;
    }

    /**
     * @return Page
     * @throws HairChooserException
     */
    public function getActivePage() : Page
    {
        return $this->getPageByID($this->getActivePageID());
    }

    public function redirectTo(string $url) : never
    {
        header('Location: '.$url);
        exit;
    }

    public function getPageModList() : Page
    {
        return $this->getPageByID(self::PAGE_MODS_LIST);
    }

    public function getAppName() : string
    {
        return t('Cyberpunk Hairs Chooser');
    }
}