<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Chart;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Table\Chart\IChartDataTable;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The chart renderer base class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ChartRenderer implements IChartRenderer
{

    /**
     * @var TemplateRendererInterface
     */
    protected $template;

    /**
     * @param TemplateRendererInterface $template
     */
    public function __construct(TemplateRendererInterface $template)
    {
        $this->template = $template;
    }

    /**
     * Renders the supplied chart input as a html string.
     *
     * @param IChartDataTable $chartData
     *
     * @return string
     * @throws InvalidArgumentException
     */
    final public function render(IChartDataTable $chartData) : string
    {
        if (!$this->accepts($chartData)) {
            throw InvalidArgumentException::format(
                'Invalid chart supplied to %s',
                get_class($this) . '::' . __FUNCTION__
            );
        }

        return $this->renderChart($chartData);
    }

    /**
     * Renders the supplied chart input as a html string.
     *
     * @param IChartDataTable $chartData
     *
     * @return string
     */
    abstract protected function renderChart(IChartDataTable $chartData) : string;
}
