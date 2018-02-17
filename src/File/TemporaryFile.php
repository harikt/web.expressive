<?php declare(strict_types=1);

namespace Dms\Web\Expressive\File;

use Dms\Common\Structure\DateTime\DateTime;
use Dms\Common\Structure\FileSystem\File;
use Dms\Core\File\IFile;
use Dms\Core\Model\Criteria\SpecificationDefinition;
use Dms\Core\Model\ISpecification;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\Entity;
use Dms\Core\Util\IClock;

/**
 * The temporary file entity.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TemporaryFile extends Entity
{
    const TOKEN = 'token';
    const FILE = 'file';
    const EXPIRY = 'expiry';

    /**
     * @var string
     */
    protected $token;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var DateTime
     */
    protected $expiry;

    /**
     * TemporaryFile constructor.
     *
     * @param string   $token
     * @param IFile    $file
     * @param DateTime $expiry
     */
    public function __construct(string $token, IFile $file, DateTime $expiry)
    {
        parent::__construct();
        $this->token = $token;
        $this->file = File::fromExisting($file);
        $this->expiry = $expiry;
    }

    /**
     * @param IClock $clock
     *
     * @return ISpecification
     */
    public static function notExpiredSpec(IClock $clock) : ISpecification
    {
        return self::expiredSpec($clock)->not();
    }

    /**
     * @param IClock $clock
     *
     * @return ISpecification
     */
    public static function expiredSpec(IClock $clock) : ISpecification
    {
        return self::specification(
            function (SpecificationDefinition $match) use ($clock) {
                return $match->where(self::EXPIRY, '<=', new DateTime($clock->utcNow()));
            }
        );
    }

    /**
     * Defines the structure of this entity.
     *
     * @param ClassDefinition $class
     */
    protected function defineEntity(ClassDefinition $class)
    {
        $class->property($this->token)->asString();

        $class->property($this->file)->asObject(File::class);

        $class->property($this->expiry)->asObject(DateTime::class);
    }

    /**
     * @return string
     */
    public function getToken() : string
    {
        return $this->token;
    }

    /**
     * @return File
     */
    public function getFile() : File
    {
        return $this->file;
    }

    /**
     * @return DateTime
     */
    public function getExpiry() : DateTime
    {
        return $this->expiry;
    }

    public function isExpired(IClock $clock)
    {
        return self::expiredSpec($clock)->isSatisfiedBy($this);
    }
}
