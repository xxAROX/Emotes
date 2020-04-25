<?php
/* Copyright (c) 2020 xxAROX. All rights reserved. */
namespace xxAROX\LanguageAPI\utils;
use xxAROX\LanguageAPI\LanguageAPI;


/**
 * Class Language
 * @package xxAROX\LanguageAPI\utils
 * @author xxAROX
 * @date 28.03.2020 - 20:42
 * @project GetDown
 */
class Language
{
	private $locale;
	private $name;
	private $cache;
	private $prefix;


	/**
	 * Language constructor.
	 * @param array $cache
	 */
	public function __construct(array $cache){
		$this->locale = $cache["localeCode"];
		$this->name = $cache["name"];
		$this->prefix = $cache["PREFIX"];
		$this->cache = $cache;
	}

	/**
	 * Function getLocale
	 * @return string|null
	 */
	public function getLocale(): ?string{
		return $this->locale;
	}

	/**
	 * Function getName
	 * @return string|null
	 */
	public function getName(): ?string{
		return $this->name;
	}

	public function getPrefix(): string{
		return $this->prefix;
	}

	/**
	 * Function translate
	 * @param string $key
	 * @param array|null $values
	 * @return string
	 */
	public function translate(string $key, ?array $values=[]): string{
		$key = str_replace("%", "", $key);

		if (!isset($this->cache["values"][$key])) {
			if (LanguageAPI::getFallbackLanguage()->isKey($key)) {
				$str = LanguageAPI::getFallbackLanguage()->translate($key, $values);
				$str = str_replace("{PREFIX}", $this->getPrefix(), $str);
				return $str;
			}
			return $key;
		}
		$res = $this->cache['values'][$key];

		if (!empty($values)) {
			preg_match_all("/{(\d+)}/", $res, $matches, PREG_OFFSET_CAPTURE);

			foreach ($matches[1] as $akey => $match) {
				$rkey = ((int)$match[0]);
				$res = str_replace("{" . $rkey . "}", $values[$rkey], $res);
			}
		}
		$res = str_replace("{PREFIX}", $this->getPrefix(), $res);
		return $res;
	}

	/**
	 * Function isKey
	 * @param string $key
	 * @return bool
	 */
	public function isKey(string $key): bool{
		$key = str_replace("%", "", $key);
		return isset($this->cache["values"][$key]);
	}
}
