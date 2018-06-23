<?php declare(strict_types=1);
namespace DATOSCZ\MapyCzGeocoder\Utils;

/**
 * Immutable representation of place associated with some coordinates
 */
class Place
{
	public const ADDRESS = 'address';
	public const STREET = 'street';
	public const QURATER = 'quarter';
	public const WARD = 'ward';
	public const MUNICIPALITY = 'municipality';
	public const DISTRICT = 'district';
	public const REGION = 'region';
	public const COUNTRY = 'country';

	private $order;

	/** @var array */
	private $items;

	/** @var Coordinates */
	private $coordinates;

	/** @var string */
	private $label;

	public function __construct(Coordinates $coordinates, string $label, array $items)
	{
		$this->order = array_flip([
			self::ADDRESS,
			self::STREET,
			self::QURATER,
			self::WARD,
			self::MUNICIPALITY,
			self::DISTRICT,
			self::REGION,
			self::COUNTRY
		]);

		$this->items = array_merge($this->order, $items);
		$this->coordinates = $coordinates;
		$this->label = $label;
	}

	public function getLabel(): string
	{
		return $this->label;
	}

	public function getCoordinates(): Coordinates
	{
		return $this->coordinates;
	}

	public function getItems(): array
	{
		return $this->items;
	}

	public function __toString(): string
	{
		return $this->label;
	}
}
