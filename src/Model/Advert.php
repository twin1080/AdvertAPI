<?php
declare(strict_types=1);

namespace AdvertAPI\Model;


use MongoDB\BSON\Persistable;
use MongoDB\BSON\UTCDateTime;

class Advert implements Persistable
{
    protected int $id;

    protected string $text;

    protected float $price;

    protected int $limit;

    protected string $banner;

    protected UTCDateTime $lastShowTime;

    protected int $shownCount = 0;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text)
    {
        $this->text = $text;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price)
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return string
     */
    public function getBanner(): string
    {
        return $this->banner;
    }

    /**
     * @param string $banner
     */
    public function setBanner(string $banner): void
    {
        $this->banner = $banner;
    }

    /**
     * @return UTCDateTime
     */
    public function getLastShowTime(): UTCDateTime
    {
        return $this->lastShowTime;
    }

    /**
     * @param \DateTime $lastShowTime
     */
    public function setLastShowTime(\DateTime $datetime): void
    {
        $this->lastShowTime = new UTCDateTime($datetime);
    }

    /**
     * @return int
     */
    public function getShownCount(): int
    {
        return $this->shownCount ?? 0;
    }

    /**
     * @param int $shownCount
     */
    public function setShownCount(int $shownCount): void
    {
        $this->shownCount = $shownCount;
    }

    public function bsonSerialize()
    {
        return get_object_vars($this);
    }

    public function bsonUnserialize(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}