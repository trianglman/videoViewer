<?php
namespace videoViewer\Entities;

/**
 * Class to hold the SeriesAlias
 *
 * @author johnj
 * @Entity
 * @table(name="seriesAlias")
 */
class SeriesAlias {
    /**
     *
     * @var int
     * @Column(type="integer")
     * @Id
     * @GeneratedValue
     */
    private $id;
    /**
     *
     * @var videoViewer\Entities\Series
     * @ManyToOne(targetEntity="videoViewer\Entities\Series", inversedBy="aliases")
     */
    private $series;
    /**
     *
     * @var string
     * @Column(length=255)
     */
    private $alias;

    /**
     *
     * @param Series $series
     * @return SeriesAlias 
     */
    public function setSeries(Series $series){
        $this->series = $series;
        return $this;
    }

    public function setAlias($alias){
        $this->alias = $alias;
        return $this;
    }

    public function getSeries(){
        return $this->series;
    }

    public function getAlias(){
        return $this->alias;
    }
}
?>
