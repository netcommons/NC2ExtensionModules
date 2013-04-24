<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * バナー登録コンポーネント
 *
 * @package     NetCommons Components
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Banner_Components_Action
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
	 * @access	public
	 */
	function Banner_Components_Action()
	{
		$this->_container =& DIContainerFactory::getContainer();
		$this->_db =& $this->_container->getComponent('DbObject');
		$this->_request =& $this->_container->getComponent('Request');
		$this->_session =& $this->_container->getComponent('Session');
	}

	/**
	 * バナー初期データを登録する
	 *
	 * @return boolean true or false
	 * @access public
	 */
	function initialize()
	{
		$configView = $this->_container->getComponent('configView');
		$moduleId = $this->_request->getParameter('module_id');
		$config = $configView->getConfigByConfname($moduleId, 'display_type');
		if ($config === false) {
			return 'error';
		}

		$blockId = $this->_request->getParameter('block_id');
		$params = array(
			'block_id' => $blockId,
			'display_type' => constant($config['conf_value'])
		);
		if (!$this->_db->insertExecute('banner_display', $params, true)) {
			return false;
		}

		$bannerView =& $this->_container->getComponent('bannerView');
		$banners = $bannerView->getBanners();
		foreach ($banners as $banner) {
			$params = array(
				'block_id' => $blockId,
				'banner_id' => $banner['banner_id'],
				'display' => _ON,
				'block_click_count' => '0'
			);
			if (!$this->_db->insertExecute('banner_block', $params, true)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * バナーデータを登録する
	 *
	 * @return boolean true or false
	 * @access public
	 */
	function setBanner()
	{
		$target = BANNER_LINK_TARGET_BLANK;
		$targetBlankFlag = $this->_request->getParameter('target_blank_flag');
		if (empty($targetBlankFlag)) {
			$target = BANNER_LINK_TARGET_SELF;
		}

		$params = array(
			'banner_name' => $this->_request->getParameter('banner_name'),
			'banner_type' => intval($this->_request->getParameter('banner_type')),
			'category_id' => intval($this->_request->getParameter('category_id')),
			'link_url' => $this->_request->getParameter('link_url'),
			'target' => $target,
			'image_url' => $this->_request->getParameter('image_url'),
			'size_flag' => intval($this->_request->getParameter('size_flag')),
			'source_code' => $this->_request->getParameter('source_code')
		);

		$isUploaded = false;
		$bannerId = $this->_request->getParameter('banner_id');
		if ($params['banner_type'] == BANNER_TYPE_IMAGE_VALUE) {
			$fileUpload =& $this->_container->getComponent('FileUpload');
			$errors = $fileUpload->getError();
			if (empty($bannerId)
				|| $errors[0] != UPLOAD_ERR_NO_FILE) {
				$isUploaded = true;
			}
		}
		if ($isUploaded) {
			$uploadAction =& $this->_container->getComponent('uploadsAction');
			$uploadFiles = $uploadAction->uploads();
			$uploadFile = $uploadFiles[0];
			$imageSize = getimagesize(FILEUPLOADS_DIR . 'banner/' . $uploadFile['physical_file_name']);

			$params['upload_id'] = $uploadFile['upload_id'];
			$params['image_path'] = '?' . ACTION_KEY . '=' . $uploadFile['action_name']
									. '&upload_id=' . $uploadFile['upload_id'];
			$params['image_width'] = $imageSize[0];
			$params['image_height'] = $imageSize[1];
		}

		if ($params['size_flag'] == _ON) {
			$params['display_width'] = intval($this->_request->getParameter('display_width'));
			$params['display_height'] =  intval($this->_request->getParameter('display_height'));
		}

	 	if (empty($bannerId)
	 		&& empty($params['size_flag'])) {
			$bannerView =& $this->_container->getComponent('bannerView');
	 		$defaultBanner = $bannerView->getDefaultBanner();
			$params['display_width'] = $defaultBanner['display_width'];
			$params['display_height'] = $defaultBanner['display_height'];

			$bannerCount = $bannerView->getBannerCount();
			$params['banner_sequence'] = $bannerCount + 1;
	 	}

		if (empty($bannerId)) {
			$result = $this->_db->insertExecute('banner', $params, true, 'banner_id');
		} else {
			$params['banner_id'] = $bannerId;
			$result = $this->_db->updateExecute('banner', $params, 'banner_id', true);
		}
		if (!$result) {
			return false;
		}

		if (!empty($bannerId)) {
			return true;
		}

		$bannerId = $result;
		$params = array(
			'block_id' => $this->_request->getParameter('block_id'),
			'banner_id' => $bannerId,
			'display' => _ON,
			'block_click_count' => '0'
		);
		if (!$this->_db->insertExecute('banner_block', $params, true)) {
			return false;
		}

		return true;
	}

	/**
	 * バナーデータを削除する
	 *
	 * @return boolean true or false
	 * @access public
	 */
	function deleteBanner()
	{
		$params = array(
			'banner_id' => $this->_request->getParameter('banner_id')
		);

		$sql = "SELECT banner_sequence, "
					. "upload_id ". 
				"FROM {banner} ".
				"WHERE banner_id = ?";
		$sequences = $this->_db->execute($sql, $params, 1, null, false);
		if ($sequences === false) {
			$this->_db->addError();
			return false;
		}
		$sequence = $sequences[0][0];
		$uploadId = $sequences[0][1];

		if (!empty($uploadId)) {
			$uploadAction =& $this->_container->getComponent('uploadsAction');
			if (!$uploadAction->delUploadsById($uploadId)) {
				return false;
			}
		}

		if (!$this->_db->deleteExecute('banner_block', $params)) {
			return false;
		}

		if (!$this->_db->deleteExecute('banner', $params)) {
			return false;
		}

		$params = array(
			$this->_request->getParameter('room_id'),
			$sequence
		);
		$sql = "UPDATE {banner} "
				. "SET banner_sequence = banner_sequence - 1 "
				. "WHERE room_id = ? "
				. "AND banner_sequence > ?";
		$result = $this->_db->execute($sql, $params);
		if($result === false) {
			$this->_db->addError();
			return false;
		}

		return true;
	}

	/**
	 * バナーシーケンス番号データを変更する
	 *
	 * @return boolean true or false
	 * @access public
	 */
	function updateBannerSequence() 
	{
		$roomId = $this->_request->getParameter('room_id');
		$dragSequence = $this->_request->getParameter('drag_sequence'); 
		$dropSequence = $this->_request->getParameter('drop_sequence');

		$params = array(
			$roomId,
			$dragSequence,
			$dropSequence
		);
		if ($dragSequence > $dropSequence) {
			$sql = "UPDATE {banner} "
					. "SET banner_sequence = banner_sequence + 1 "
					. "WHERE room_id = ? "
					. "AND banner_sequence < ? "
					. "AND banner_sequence > ?";
		} else {
			$sql = "UPDATE {banner} "
					. "SET banner_sequence = banner_sequence - 1 "
					. "WHERE room_id = ? "
					. "AND banner_sequence > ? "
					. "AND banner_sequence <= ?";
		}

		$result = $this->_db->execute($sql, $params);
		if($result === false) {
			$this->_db->addError();
			return false;
		}

		if ($dragSequence > $dropSequence) {
			$dropSequence++;
		}
		$params = array(
			$dropSequence,
			$this->_request->getParameter('drag_banner_id'),
			$roomId
		);
		$sql = "UPDATE {banner} "
				. "SET banner_sequence = ? "
				. "WHERE banner_id = ? "
				. "AND room_id = ?";
		$result = $this->_db->execute($sql, $params);
		if($result === false) {
			$this->_db->addError();
			return false;
		}

		return true;
	}

	/**
	 * カテゴリデータを登録する
	 *
	 * @return boolean true or false
	 * @access public
	 */
	function setCategory()
	{
		$params = array(
			'category_name' => $this->_request->getParameter('category_name')
		);

		$categoryId = $this->_request->getParameter('category_id');
		if (empty($categoryId)) {
			$bannerView =& $this->_container->getComponent('bannerView');
			$categoryCount = $bannerView->getCategoryCount();
			$params['category_sequence'] = $categoryCount + 1;
			$result = $this->_db->insertExecute('banner_category', $params, true, 'category_id');
		} else {
			$params['category_id'] = $categoryId;
			$result = $this->_db->updateExecute('banner_category', $params, 'category_id', true);
		}
		if (!$result) {
			return false;
		}

		return true;
	}

	/**
	 * カテゴリデータを削除する
	 *
	 * @return boolean true or false
	 * @access public
	 */
	function deleteCategory()
	{
		$params = array(
			'category_id' => $this->_request->getParameter('category_id')
		);

		$sql = "SELECT category_sequence ". 
				"FROM {banner_category} ".
				"WHERE category_id = ?";
		$sequences = $this->_db->execute($sql, $params, 1, null, false);
		if ($sequences === false) {
			$this->_db->addError();
			return false;
		}
		$sequence = $sequences[0][0];

		if (!$this->_db->deleteExecute('banner_category', $params)) {
			return false;
		}

		$params = array(
			$this->_request->getParameter('room_id'),
			$sequence
		);
		$sql = "UPDATE {banner_category} "
				. "SET category_sequence = category_sequence - 1 "
				. "WHERE room_id = ? "
				. "AND category_sequence > ?";
		$result = $this->_db->execute($sql, $params);
		if($result === false) {
			$this->_db->addError();
			return false;
		}

		return true;
	}

	/**
	 * カテゴリシーケンス番号データを変更する
	 *
	 * @return boolean true or false
	 * @access public
	 */
	function updateCategorySequence() 
	{
		$roomId = $this->_request->getParameter('room_id');
		$dragSequence = $this->_request->getParameter('drag_sequence'); 
		$dropSequence = $this->_request->getParameter('drop_sequence');

		$params = array(
			$roomId,
			$dragSequence,
			$dropSequence
		);
		if ($dragSequence > $dropSequence) {
			$sql = "UPDATE {banner_category} "
					. "SET category_sequence = category_sequence + 1 "
					. "WHERE room_id = ? "
					. "AND category_sequence < ? "
					. "AND category_sequence > ?";
		} else {
			$sql = "UPDATE {banner_category} "
					. "SET category_sequence = category_sequence - 1 "
					. "WHERE room_id = ? "
					. "AND category_sequence > ? "
					. "AND category_sequence <= ?";
		}

		$result = $this->_db->execute($sql, $params);
		if($result === false) {
			$this->_db->addError();
			return false;
		}

		if ($dragSequence > $dropSequence) {
			$dropSequence++;
		}
		$params = array(
			$dropSequence,
			$this->_request->getParameter('drag_category_id'),
			$roomId
		);
		$sql = "UPDATE {banner_category} "
				. "SET category_sequence = ? "
				. "WHERE category_id = ? "
				. "AND room_id = ?";
		$result = $this->_db->execute($sql, $params);
		if($result === false) {
			$this->_db->addError();
			return false;
		}

		return true;
	}

	/**
	 * 表示方法データを変更する
	 *
	 * @return boolean true or false
	 * @access public
	 */
	function setDisplayType()
	{
		$blockId = $this->_request->getParameter('block_id');
		$params = array(
			'block_id' => $blockId,
			'display_type' => intval($this->_request->getParameter('display_type'))
		);
		if (!$this->_db->updateExecute('banner_display', $params, 'block_id', true)) {
			return false;
		}
	}

	/**
	 * 表示バナーデータを変更する
	 *
	 * @return boolean true or false
	 * @access public
	 */
	function changeDislpay()
	{
		$blockId = $this->_request->getParameter('block_id');
		$roomId = $this->_request->getParameter('room_id');
		$bannerId = $this->_request->getParameter('banner_id');
		$categoryId = $this->_request->getParameter('category_id');
		$display = $this->_request->getParameter('display');
		if (empty($display)) {
			$display = _OFF;
		} else {
			$display = _ON;
		}

		$bannerView =& $this->_container->getComponent('bannerView');
		$params = array(
			$blockId,
			$roomId
		);
		$sql = "SELECT B.banner_id, "
				. "BB.block_id "
				. $bannerView->getBannerFromSql();

		if (!empty($categoryId)) {
			$params[] = $categoryId;
			$sql .= "AND B.category_id = ? ";
		} elseif (!empty($bannerId)) {
			$params[] = $bannerId;
			$sql .= "AND B.banner_id = ? ";
		} 

		$bannerBlocks = $this->_db->execute($sql, $params);
		if ($bannerBlocks === false) {
			$this->_db->addError();
			return false;
		}
		if (empty($bannerBlocks)) {
			return true;
		}

		$params = array(
			'block_id' => $blockId,
			'display' => $display,
			'block_click_count' => '0'
		);
		$updateBannerIds = array();
		foreach ($bannerBlocks as $bannerBlock) {
			if (!empty($bannerBlock['block_id'])) {
				$updateBannerIds[] = $bannerBlock['banner_id'];
				continue;
			}

			if ($display == _ON) {
				$params['banner_id'] = $bannerBlock['banner_id'];
				$result = $this->_db->insertExecute('banner_block', $params, true);
			}
		}

		if (!empty($updateBannerIds)) {
			$params = array(
				$display,
				$blockId
			);
			$sql = "UPDATE {banner_block} SET "
						. "display = ? "
					. "WHERE block_id = ? "
					. "AND banner_id IN (" . implode(',', $updateBannerIds) . ")";
			$result = $this->_db->execute($sql, $params);
			if ($result === false) {
				$this->_db->addError();
				return false;
			}
		}

		return true;
	}

	/**
	 * クリック数を増加する
	 *
	 * @return boolean true or false
	 * @access public
	 */
	function incrementClickCount()
	{
		$params = array(
			$this->_request->getParameter('block_id'),
			$this->_request->getParameter('banner_id')
		);
		$sql = "UPDATE {banner_block} SET "
					. "block_click_count = block_click_count + 1 "
				. "WHERE block_id = ? "
				. "AND banner_id = ? ";
		;
		$result = $this->_db->execute($sql, $params);
		if ($result === false) {
			$this->_db->addError();
			return false;
		}

		$params = array(
			$this->_request->getParameter('banner_id'),
			$this->_request->getParameter('room_id')
		);
		$sql = "UPDATE {banner} SET "
					. "all_click_count = all_click_count + 1 "
				. "WHERE banner_id = ? "
				. "AND room_id = ? ";
		;
		$result = $this->_db->execute($sql, $params);
		if ($result === false) {
			$this->_db->addError();
			return false;
		}

		return true;
	}
}
?>