<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * バナー取得コンポーネント
 *
 * @package     NetCommons Components
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Banner_Components_View
{
	/**
	 * @var DBオブジェクトを保持
	 *
	 * @access	private
	 */
	var $_db = null;
	
	/**
	 * @var DIコンテナを保持
	 *
	 * @access	private
	 */
	var $_container = null;

	/**
	 * @var Requestオブジェクトを保持
	 *
	 * @access	private
	 */
	var $_request = null;

	/**
	 * @var Sessionオブジェクトを保持
	 *
	 * @access	private
	 */
	var $_session = null;

	/**
	 * コンストラクター
	 *
	 * @access public
	 */
	function Banner_Components_View() 
	{
		$this->_container =& DIContainerFactory::getContainer();
		$this->_db =& $this->_container->getComponent('DbObject');
		$this->_request =& $this->_container->getComponent('Request');
		$this->_session =& $this->_container->getComponent('Session');
	}

	/**
	 * バナーが存在するか判断する
	 *
	 * @return boolean true:存在する、false:存在しない
	 * @access public
	 */
	function bannerExists()
	{
		$params = array(
			$this->_request->getParameter('banner_id'),
			$this->_request->getParameter('room_id')
		);
		$sql = "SELECT banner_id "
				. "FROM {banner} "
				. "WHERE banner_id = ? "
				. "AND room_id = ?";
		$bannerIds = $this->_db->execute($sql, $params);
		if ($bannerIds === false) {
			$this->_db->addError();
		}

		if (count($bannerIds) > 0) {
			return true;
		}

		return false;
	}

	/**
	 * バナー一覧データを取得する
	 *
	 * @return array バナー一覧データ配列
	 * @access public
	 */
	function &getBanners() 
	{
		$limit = $this->_request->getParameter('limit');
		$offset = $this->_request->getParameter('offset');

		$sortColumn = $this->_request->getParameter('sort_col');
		if (empty($sortColumn)) {
			$sortColumn = 'banner_sequence';
		}
		$sortDirection = $this->_request->getParameter('sort_dir');
		if (empty($sortDirection)) {
			$sortDirection = "ASC";
		}
		$orderParams[$sortColumn] = $sortDirection;

		$params = array(
			$this->_request->getParameter('block_id'),
			$this->_request->getParameter('room_id')
		);
		$categoryId = $this->_request->getParameter('category_id');
		$categoryWhereSql = "";
		if (!empty($categoryId)) {
			$categoryWhereSql = "AND category_id = ? ";
			$params[] = $categoryId;
		}
		$sql = $this->_getBannerSelectSql()
				. $this->getBannerFromSql()
				. $categoryWhereSql
				. $this->_db->getOrderSQL($orderParams, array('banner_sequence', 'block_click_count', 'all_click_count', 'insert_user_name', 'insert_time'));
		$banners = $this->_db->execute($sql, $params, $limit, $offset, true, array($this, '_fetchBanner'));
		if ($banners === false) {
			$this->_db->addError();
		}

		return $banners;
	}

	/**
	 * バナーデータをフェッチする
	 *
	 * @param array $recordSet バナーデータADORecordSet
	 * @return array バナー一覧データ配列
	 * @access public
	 */
	function &_fetchBanner(&$recordSet) 
	{
		$banners = array();
		while ($banner = $recordSet->fetchRow()) {
			if (!empty($banner['display'])) {
				$banner['checkedAttribute'] = ' checked="checked"';
			}

			if (empty($banner['block_click_count'])) {
				$banner['block_click_count'] = 0;
			}
			if ($banner['banner_type'] == BANNER_TYPE_IMAGE_VALUE) {
				$width = $banner['image_width'];
				$height = $banner['image_height'];
				$ratio = $height / $width;

				$widthRatio = $width / BANNER_THUMBNAIL_WIDTH;
				$heightRatio = $height / BANNER_THUMBNAIL_HEIGHT;
				
				if ($widthRatio > $heightRatio) {
					$height = BANNER_THUMBNAIL_HEIGHT;
					$widht = intval($height / $ratio);
					$top = 0;
					$right = intval(($widht + BANNER_THUMBNAIL_WIDTH) / 2);
					$bottom = BANNER_THUMBNAIL_HEIGHT;
					$left = intval(($widht - BANNER_THUMBNAIL_WIDTH) / 2);
					$marginLeft = $left * -1;
					$marginTop = $top;
				} else {
					$widht = BANNER_THUMBNAIL_WIDTH;
					$height = intval($widht * $ratio);
					$top = intval(($height - BANNER_THUMBNAIL_HEIGHT) / 2);
					$right = BANNER_THUMBNAIL_WIDTH;
					$bottom = intval(($height + BANNER_THUMBNAIL_HEIGHT) / 2);
					$left = 0;
					$marginLeft = $left;
					$marginTop = $top * -1;
				}

				$banner['thumbnailStyle'] = sprintf(BANNER_THUMBNAIL_STYLE, 
													$widht,
													$height,
													$top,
													$right,
													$bottom,
													$left,
													$marginLeft,
													$marginTop);
			}
			$banners[] = $banner;
		}

		return $banners;
	}

	/**
	 * 表示対象のバナー一覧データを取得する
	 *
	 * @return array バナー一覧データ配列
	 * @access public
	 */
	function &getCheckedBanners() 
	{
		$params = array(
			$this->_request->getParameter('block_id'),
			$this->_request->getParameter('room_id'),
			_ON
		);

		$sql = $this->_getBannerSelectSql()
				. $this->getBannerFromSql()
				. "AND BB.display = ? "
				. "ORDER BY B.banner_sequence";
		$banners = $this->_db->execute($sql, $params);
		if ($banners === false) {
			$this->_db->addError();
		}

		return $banners;
	}

	/**
	 * 表示対象のバナー一覧データを取得する
	 *
	 * @return array バナー一覧データ配列
	 * @access public
	 */
	function &getCheckedBannersCount() 
	{
		$params = array(
			$this->_request->getParameter('block_id'),
			$this->_request->getParameter('room_id'),
			_ON
		);

		$sql =  "SELECT COUNT(B.banner_id) "
				. $this->getBannerFromSql()
				. "AND BB.display = ?";
		$bannerCounts = $this->_db->execute($sql, $params, null, null, false);
		if ($bannerCounts === false) {
			$this->_db->addError();
		}

		return $bannerCounts[0][0];
	}

	/**
	 * 表示対象のバナーデータを順番に取得する
	 *
	 * @return array バナーデータ配列
	 * @access public
	 */
	function &getSequenceBanner() 
	{
		$blockId = $this->_request->getParameter('block_id');
		$offset = $this->_session->getParameter('banner_sequence' . $blockId);
		$maxSequence = $this->getCheckedBannersCount() - 1;
		if (empty($offset)
			|| $offset > $maxSequence) {
			$offset = 0;
		}
		$this->_session->setParameter('banner_sequence' . $blockId, $offset + 1);

		$params = array(
			$blockId,
			$this->_request->getParameter('room_id'),
			_ON
		);
		$sql = $this->_getBannerSelectSql()
				. $this->getBannerFromSql()
				. "AND BB.display = ? "
				. "ORDER BY B.banner_sequence";
		$banners = $this->_db->execute($sql, $params, 1, $offset);
		if ($banners === false) {
			$this->_db->addError();
		}
		if (empty($banners)) {
			return $banners;
		}

		return $banners[0];
	}

	/**
	 * 表示対象のバナーデータをランダムに取得する
	 *
	 * @return array バナーデータ配列
	 * @access public
	 */
	function &getRandomBanner() 
	{
		$maxSequence = $this->getCheckedBannersCount() - 1;
		$offset = rand(0, $maxSequence);

		$params = array(
			$this->_request->getParameter('block_id'),
			$this->_request->getParameter('room_id'),
			_ON
		);
		$sql = $this->_getBannerSelectSql()
				. $this->getBannerFromSql()
				. "AND BB.display = ? "
				. "ORDER BY B.banner_sequence";
		$banners = $this->_db->execute($sql, $params, 1, $offset);
		if ($banners === false) {
			$this->_db->addError();
		}
		if (empty($banners)) {
			return $banners;
		}

		return $banners[0];
	}

	/**
	 * バナーデータSQLのSELECT句を取得する
	 *
	 * @return string バナーデータSQLのSELECT句
	 * @access public
	 */
	function &_getBannerSelectSql() 
	{
		$sql = "SELECT B.banner_id, "
					. "B.banner_name, "
					. "B.banner_type, "
					. "B.all_click_count, "
					. "B.link_url, "
					. "B.target, "
					. "B.image_url, "
					. "B.image_path, "
					. "B.image_width, "
					. "B.image_height, "
					. "B.size_flag, "
					. "B.display_width, "
					. "B.display_height, "
					. "B.source_code, "
					. "B.insert_time, "
					. "B.insert_user_id, "
					. "B.insert_user_name, "
					. "BB.display, "
					. "BB.block_click_count ";

		return $sql;
	}

	/**
	 * バナーデータSQLのFROM句を取得する
	 *
	 * @return string バナーデータSQLのFROM句
	 * @access public
	 */
	function &getBannerFromSql() 
	{
		$sql = "FROM {banner} B "
				. "LEFT JOIN {banner_block} BB "
					. "ON B.banner_id = BB.banner_id "
					. "AND BB.block_id = ? "
				. "WHERE B.room_id = ? ";

		return $sql;
	}

	/**
	 * バナー用デフォルトデータを取得する
	 *
	 * @return array バナー用デフォルトデータ配列
	 * @access public
	 */
	function &getDefaultBanner()
	{
		$container =& DIContainerFactory::getContainer();
		$configView =& $container->getComponent('configView');
		$moduleID = $this->_request->getParameter('module_id');
		$config = $configView->getConfig($moduleID, false);
		if ($config === false) {
			return $config;
		}

		$banner = array(
			'category_id' => '0',
			'banner_type' => constant($config['banner_type']['conf_value']),
			'link_url' => constant($config['link_url']['conf_value']),
			'target' => $config['target']['conf_value'],
			'image_url' => constant($config['image_url']['conf_value']),
			'size_flag' => constant($config['size_flag']['conf_value']),
			'display_width' => $config['display_width']['conf_value'],
			'display_height' => $config['display_height']['conf_value'],
		);

		return $banner;
	}

	/**
	 * バナーデータを取得する
	 *
	 * @return array バナー一覧データ配列
	 * @access public
	 */
	function &getBanner() 
	{
		$params = array(
			$this->_request->getParameter('banner_id'),
			$this->_request->getParameter('room_id')
		);
		$sql = "SELECT banner_id, "
					. "banner_name, "
					. "banner_type, "
					. "category_id, "
					. "link_url, "
					. "target, "
					. "image_url, "
					. "image_path, "
					. "image_width, "
					. "image_height, "
					. "size_flag, "
					. "display_width, "
					. "display_height, "
					. "source_code "
				. "FROM {banner} "
				. "WHERE banner_id = ? "
				. "AND room_id = ?";
		$banners = $this->_db->execute($sql, $params, null, null, true, array($this, '_fetchBanner'));
		if ($banners === false) {
			$this->_db->addError();
		}
		$banner = $banners[0];

		return $banner;
	}

	/**
	 * ルームIDのバナー件数を取得する
	 *
	 * @return string バナー件数
	 * @access public
	 */
	function &getBannerCount()
	{
		$params = array(
			'room_id' => $this->_request->getParameter('room_id')
		);
		$count = $this->_db->countExecute('banner', $params);
		return $count;
	}

	/**
	 * ルームID、カテゴリIDのバナー件数を取得する
	 *
	 * @return string バナー件数
	 * @access public
	 */
	function &getNarrowBannerCount()
	{
		$params = array(
			'room_id' => $this->_request->getParameter('room_id')
		);

		$categoryId = $this->_request->getParameter('category_id');
		if (!empty($categoryId)) {
			$params['category_id'] = $categoryId;
		}

		$count = $this->_db->countExecute('banner', $params);
		return $count;
	}

	/**
	 * バナーシーケンス番号データを取得する
	 *
	 * @return array バナーシーケンス番号データ配列
	 * @access public
	 */
	function &getBannerSequence() 
	{
		$params = array(
			$this->_request->getParameter('drag_banner_id'),
			$this->_request->getParameter('drop_banner_id'),
			$this->_request->getParameter('room_id')
		);
		$sql = "SELECT banner_id, "
					. "banner_sequence "
				. "FROM {banner} "
				. "WHERE (banner_id = ? "
						. "OR banner_id = ?) "
				. "AND room_id = ?";
		$result = $this->_db->execute($sql, $params);
		if ($result === false ||
			count($result) != 2) {
			$this->_db->addError();
			return false;
		}

		$sequences[$result[0]['banner_id']] = $result[0]['banner_sequence'];
		$sequences[$result[1]['banner_id']] = $result[1]['banner_sequence'];

		return $sequences;
	}

	/**
	 * カテゴリ一覧データを取得する
	 *
	 * @return array カテゴリ一覧データ
	 * @access public
	 */
	function &getCategories()
	{
		$params = array(
			$this->_request->getParameter('room_id')
		);
		$sql = "SELECT category_id, "
					. "category_name "
				. "FROM {banner_category} "
				. "WHERE room_id = ? "
				. "ORDER BY category_sequence";
		$categories = $this->_db->execute($sql, $params);
		if ($categories === false) {
			$this->_db->addError();
		}

		return $categories;
	}

	/**
	 * カテゴリが存在するか判断する
	 *
	 * @return boolean true:存在する、false:存在しない
	 * @access public
	 */
	function categoryExists()
	{
		$params = array(
			$this->_request->getParameter('category_id'),
			$this->_request->getParameter('room_id')
		);
		$sql = "SELECT category_id "
				. "FROM {banner_category} "
				. "WHERE category_id = ? "
				. "AND room_id = ?";
		$categoryIds = $this->_db->execute($sql, $params);
		if ($categoryIds === false) {
			$this->_db->addError();
		}

		if (count($categoryIds) > 0) {
			return true;
		}

		return false;
	}

	/**
	 * ルームIDのカテゴリ件数を取得する
	 *
	 * @return string カテゴリ件数
	 * @access public
	 */
	function &getCategoryCount()
	{
		$params = array(
			'room_id' => $this->_request->getParameter('room_id')
		);
		$count = $this->_db->countExecute('banner_category', $params);
		return $count;
	}

	/**
	 * カテゴリシーケンス番号データを取得する
	 *
	 * @return array カテゴリシーケンス番号データ配列
	 * @access public
	 */
	function &getCategorySequence() 
	{
		$params = array(
			$this->_request->getParameter('drag_category_id'),
			$this->_request->getParameter('drop_category_id'),
			$this->_request->getParameter('room_id')
		);
		$sql = "SELECT category_id, "
					. "category_sequence "
				. "FROM {banner_category} "
				. "WHERE (category_id = ? "
						. "OR category_id = ?) "
				. "AND room_id = ?";
				$result = $this->_db->execute($sql, $params);
		if ($result === false ||
			count($result) != 2) {
			$this->_db->addError();
			return false;
		}

		$sequences[$result[0]['category_id']] = $result[0]['category_sequence'];
		$sequences[$result[1]['category_id']] = $result[1]['category_sequence'];

		return $sequences;
	}

	/**
	 * 表示方法データを取得する
	 *
	 * @return  array 表示方法データ
	 * @access public
	 */
	function &getBannerDisplay()
	{
		$params = array(
			$this->_request->getParameter('block_id')
		);
		$sql = "SELECT display_type "
				. "FROM {banner_display} "
				. "WHERE block_id = ?";
		$bannerDisplays = $this->_db->execute($sql, $params);
		if ($bannerDisplays === false) {
			$this->_db->addError();
			return $bannerDisplays;
		}

		return $bannerDisplays[0];
	}
}
?>