<?php

use phpDocumentor\Reflection\Types\This;

defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		if (!$this->login) {
			redirect(base_url('Auth'));
		}
	}
	public function index()
	{
		$data['html']['title'] = 'Dasboard';
		$this->template($data);
	}

	public function itemTypeList()
	{
		$tableName = 'itemtype';

		$join = array(
			array('account', 'account.pkey=' . $tableName . '.createon', 'left'),
			array('role', 'role.pkey=account.role', 'left'),
		);
		$select = '
			' . $tableName . '.*,
			account.name as createname,
			account.role as createrole,
			role.name as rolename,
		';

		$dataList = $this->getDataRow($tableName, $select, '', '', $join);
		$data['html']['title'] = 'List Kondisi Barang';
		$data['html']['dataList'] = $dataList;
		$data['html']['tableName'] = $tableName;
		$data['html']['form'] = get_class($this) . '/itemType';
		$data['url'] = 'admin/itemTypeList';
		$this->template($data);
	}

	public function itemType($id = '')
	{
		$tableName = 'itemtype';
		$tableDetail = '';
		$baseUrl = get_class($this) . '/' . __FUNCTION__;
		$detailRef = '';
		$formData = array(
			'pkey' => 'pkey',
			'name' => 'name',
			'createon' => 'sesionid',
			'time' => 'time',
		);
		$formDetail = array();

		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if (empty($_POST['action'])) redirect(base_url($baseUrl . 'List'));
			//validate form
			$arrMsgErr = array();
			if (empty($_POST['name']))
				array_push($arrMsgErr, "Nama wajib Di isi");


			$this->session->set_flashdata('arrMsgErr', $arrMsgErr);
			//validate form
			if (empty(count($arrMsgErr)))
				switch ($_POST['action']) {
					case 'add':
						$refkey = $this->insert($tableName, $this->dataForm($formData));
						$this->insertDetail($tableDetail, $formDetail, $refkey);
						redirect(base_url($baseUrl . 'List')); //wajib terakhir
						break;
					case 'update':
						$this->update($tableName, $this->dataForm($formData), array('pkey' => $_POST['pkey']));
						$this->updateDetail($tableDetail, $formDetail, $detailRef, $id);
						redirect(base_url($baseUrl . 'List'));
						break;
				}
		}

		if (!empty($id)) {
			$dataRow = $this->getDataRow($tableName, '*', array('pkey' => $id), 1)[0];
			$this->dataFormEdit($formData, $dataRow);
		}

		$data['html']['baseUrl'] = $baseUrl;
		$data['html']['title'] = 'Input Data Kondisi Barang';
		$data['html']['err'] = $this->genrateErr();
		$data['url'] = 'admin/' . __FUNCTION__ . 'Form';
		$this->template($data);
	}

	public function itemList()
	{
		$tableName = 'item';

		$join = array(
			array('account', 'account.pkey=' . $tableName . '.createon', 'left'),
			array('role', 'role.pkey=account.role', 'left'),
			array('itemtype', 'itemtype.pkey=' . $tableName . '.typekey', 'left'),
		);
		$select = '
			' . $tableName . '.*,
			account.name as createname,
			account.role as createrole,
			role.name as rolename,
			itemtype.name as typename,
		';

		$dataList = $this->getDataRow($tableName, $select, '', '', $join);
		$data['html']['title'] = 'List Barang';
		$data['html']['dataList'] = $dataList;
		$data['html']['tableName'] = $tableName;
		$data['html']['form'] = get_class($this) . '/item';
		$data['url'] = 'admin/itemList';
		$this->template($data);
	}

	public function item($id = '')
	{
		$tableName = 'item';
		$tableDetail = '';
		$baseUrl = get_class($this) . '/' . __FUNCTION__;
		$detailRef = '';
		$formData = array(
			'pkey' => 'pkey',
			'name' => 'name',
			'typekey' => 'itemTypeKey',
			'createon' => 'sesionid',
			'time' => 'time',
			'unitname' => 'unitname',
		);
		$formDetail = array();

		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if (empty($_POST['action'])) redirect(base_url($baseUrl . 'List'));
			//validate form
			$arrMsgErr = array();
			if (empty($_POST['name']))
				array_push($arrMsgErr, "Nama wajib Di isi");
			if (empty($_POST['itemTypeKey']))
				array_push($arrMsgErr, "Type Barang wajib Di isi");


			$this->session->set_flashdata('arrMsgErr', $arrMsgErr);
			//validate form
			if (empty(count($arrMsgErr)))
				switch ($_POST['action']) {
					case 'add':
						$refkey = $this->insert($tableName, $this->dataForm($formData));
						$this->insertDetail($tableDetail, $formDetail, $refkey);
						redirect(base_url($baseUrl . 'List')); //wajib terakhir
						break;
					case 'update':
						$this->update($tableName, $this->dataForm($formData), array('pkey' => $_POST['pkey']));
						$this->updateDetail($tableDetail, $formDetail, $detailRef, $id);
						redirect(base_url($baseUrl . 'List'));
						break;
				}
		}

		if (!empty($id)) {
			$dataRow = $this->getDataRow($tableName, '*', array('pkey' => $id), 1)[0];
			$this->dataFormEdit($formData, $dataRow);
		}
		$itemType = $this->getDataRow('itemtype', '*', array('status' => 1));

		$data['html']['itemType'] = $itemType;
		$data['html']['baseUrl'] = $baseUrl;
		$data['html']['title'] = 'Input Data Barang';
		$data['html']['err'] = $this->genrateErr();
		$data['url'] = 'admin/' . __FUNCTION__ . 'Form';
		$this->template($data);
	}

	public function itemInList()
	{
		$tableName = 'itemin';

		$join = array(
			array('account', 'account.pkey=' . $tableName . '.createon', 'left'),
			array('role', 'role.pkey=account.role', 'left'),
			array('item', 'item.pkey=' . $tableName . '.itemkey', 'left'),
			array('itemtype', 'itemtype.pkey=item.typekey', 'left'),
			array('worker', 'worker.pkey=' . $tableName . '.workerkey', 'left'),
			array('team', 'team.pkey=' . $tableName . '.teamkey', 'left'),
		);
		$select = '
			' . $tableName . '.*,
			account.name as createname,
			account.role as createrole,
			role.name as rolename,
			item.name as itemname,
			itemtype.name as itemtypename,
			worker.name as workername,
			team.name as teamname,
		';

		$dataList = $this->getDataRow($tableName, $select, '', '', $join);
		$data['html']['title'] = 'List Barang Masuk';
		$data['html']['dataList'] = $dataList;
		$data['html']['tableName'] = $tableName;
		$data['html']['form'] = get_class($this) . '/itemIn';
		$data['url'] = 'admin/itemInList';
		$this->template($data);
	}
	public function itemIn($id = '')
	{
		$tableName = 'itemin';
		$tableDetail = '';
		$baseUrl = get_class($this) . '/' . __FUNCTION__;
		$detailRef = '';
		$formData = array(
			'pkey' => 'pkey',
			'itemkey' => 'itemKey',
			'count' => 'count',
			'createon' => 'sesionid',
			'time' => 'time',
			'note' => 'note',
			'workerkey' => 'workerKey',
			'teamkey' => 'teamKey',
			'timedate' => 'timedate',
		);
		$formDetail = array();
		$itemTable = 'item';
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if (empty($_POST['action'])) redirect(base_url($baseUrl . 'List'));
			//validate form
			$arrMsgErr = array();
			if (empty($_POST['count']))
				array_push($arrMsgErr, "Jumlah wajib Di isi");
			if (empty($_POST['itemKey']))
				array_push($arrMsgErr, "Nama Barang wajib Di isi");
			if (empty($_POST['workerKey']))
				array_push($arrMsgErr, "Nama Teknisi wajib Di isi");
			if (empty($_POST['teamKey']))
				array_push($arrMsgErr, "Nama Team wajib Di isi");


			$this->session->set_flashdata('arrMsgErr', $arrMsgErr);
			//validate form
			if (empty(count($arrMsgErr)))
				switch ($_POST['action']) {
					case 'add':
						$_POST['timedate'] = strtotime(date('d M Y'));
						$refkey = $this->insert($tableName, $this->dataForm($formData));
						$this->insertDetail($tableDetail, $formDetail, $refkey);
						$item = $this->getDataRow($itemTable, 'stock', array('pkey' => $_POST['itemKey']))[0];
						$item['stock'] = $item['stock'] + $_POST['count'];
						$this->update($itemTable, array('stock' => $item['stock']), array('pkey' => $_POST['itemKey']));
						//chek Item /pembuatan Log
						$joinItem = array(
							array('itemtype', 'itemtype.pkey=' . $itemTable . '.typekey', 'left'),
						);
						$selectitem = $itemTable . '.*,
						itemtype.name as typename';
						$item = $this->getDataRow($itemTable, $selectitem, array($itemTable . '.pkey' => $_POST['itemKey']), '', $joinItem)[0];
						//tambah logs
						$account = $this->getDataRow('account', '*', array('pkey' => $this->id))[0];
						$logs = array(
							'name' => 'barang masuk',
							'log' => $account['name'] . ' melakukan input barang masuk ' . $item['name'] . '_' . $item['typename'] . ' sebanyak :' . $_POST['count'] . ' ' . $item['unitname'],
							'time' => strtotime('now'),
						);
						$this->insert('logs', $logs);

						redirect(base_url($baseUrl . 'List')); //wajib terakhir
						break;
					case 'update':
						$dataEdit = $this->getDataRow($tableName, 'count,itemkey,status', array('pkey' => $_POST['pkey']))[0];
						if ($dataEdit['status'] == 1)
							redirect(base_url($baseUrl . 'List'));
						//pembuatan logs
						$joinItem = array(
							array('itemtype', 'itemtype.pkey=' . $itemTable . '.typekey', 'left'),
						);
						$selectitem = $itemTable . '.*,
						itemtype.name as typename';
						$item = $this->getDataRow($itemTable, $selectitem, array($itemTable . '.pkey' => $dataEdit['itemkey']), '', $joinItem)[0];
						$account = $this->getDataRow('account', 'name', array('pkey' => $this->id))[0];
						$logs = array(
							'name' => 'barang masuk',
							'log' => $account['name'] . ' melakukan perubahan barang masuk tanggal ' . date('d/m/Y H:i', $item['time']) . ' ' . $item['name'] . '_' . $item['typename'] . ' Id_Transaksi :' . $_POST['pkey'],
							'time' => strtotime('now'),
						);
						$this->insert('logs', $logs);

						//kebalikan dulu stock
						$item = $this->getDataRow($itemTable, 'stock', array('pkey' => $dataEdit['itemkey']))[0];
						$item['stock'] = $item['stock'] - $dataEdit['count'];
						$this->update($itemTable, array('stock' => $item['stock']), array('pkey' => $dataEdit['itemkey']));
						//update ulang stock
						$item = $this->getDataRow($itemTable, 'stock', array('pkey' => $_POST['itemKey']))[0];
						$item['stock'] = $item['stock'] + $_POST['count'];
						$this->update($itemTable, array('stock' => $item['stock']), array('pkey' => $_POST['itemKey']));

						$this->update($tableName, $this->dataForm($formData), array('pkey' => $_POST['pkey']));
						$this->updateDetail($tableDetail, $formDetail, $detailRef, $id);

						redirect(base_url($baseUrl . 'List'));
						break;
				}
		}

		if (!empty($id)) {
			$dataRow = $this->getDataRow($tableName, '*', array('pkey' => $id), 1)[0];
			$this->dataFormEdit($formData, $dataRow);
		}
		//item
		$itemSelect = $itemTable . '.*,
		itemtype.name itemtypename
		';
		$itemJoint = array(
			array('itemtype', 'itemtype.pkey=' . $itemTable . '.typekey', 'left'),
		);
		$item = $this->getDataRow('item', $itemSelect, '', '', $itemJoint);
		//item 


		$worker = $this->getDataRow('worker', 'pkey,name');
		$team = $this->getDataRow('team', 'pkey,name');


		$data['html']['team'] = $team;
		$data['html']['worker'] = $worker;
		$data['html']['item'] = $item;
		$data['html']['baseUrl'] = $baseUrl;
		$data['html']['title'] = 'Input Data Barang';
		$data['html']['err'] = $this->genrateErr();
		$data['url'] = 'admin/' . __FUNCTION__ . 'Form';
		$this->template($data);
	}

	public function itemOutList()
	{
		$tableName = 'itemout';

		$join = array(
			array('account', 'account.pkey=' . $tableName . '.createon', 'left'),
			array('role', 'role.pkey=account.role', 'left'),
		);
		$select = '
			' . $tableName . '.*,
			account.name as createname,
			account.role as createrole,
			role.name as rolename,
		';

		$dataList = $this->getDataRow($tableName, $select, '', '', $join);
		$data['html']['title'] = 'List Barang Keluar';
		$data['html']['dataList'] = $dataList;
		$data['html']['tableName'] = $tableName;
		$data['html']['form'] = get_class($this) . '/itemOut';
		$data['url'] = 'admin/itemOutList';
		$this->template($data);
	}
	public function itemOut($id = '')
	{
		$tableName = 'itemOut';
		$tableDetail = 'detail_itemout';
		$tableDetailTeknisi = 'detail_itemoutworker';
		$baseUrl = get_class($this) . '/' . __FUNCTION__;
		$detailRef = 'refkey';
		$formData = array(
			'pkey' => 'pkey',
			'createon' => 'sesionid',
			'time' => 'time',
			'note' => 'note',
			'teamkey' => 'teamKey',
			'timedate' => 'timedate',
		);
		$formDetail = array(
			'pkey' => 'detailKey',
			'refkey' => 'refkey',
			'itemkey' => 'itemKey',
			'count' => 'count',
			'timedate' => 'timedateDetail',
		);

		$itemTable = 'item';
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if (empty($_POST['action'])) redirect(base_url($baseUrl . 'List'));
			//validate form
			$arrMsgErr = array();
			if (empty($_POST['teamKey']))
				array_push($arrMsgErr, "Nama Team wajib Di isi");

			foreach ($formDetail as $key => $value) {
				unset($_POST[$value][0]);
			}
			if (!isset($_POST['workerKey']))
				array_push($arrMsgErr, "Nama Teknisi wajib Di isi");

			foreach ($_POST['detailKey'] as $key => $value) {
				if (empty($_POST['count'][$key]))
					array_push($arrMsgErr, "jumlah barang wajib di isi semua");
			}

			$itemSelect = 'item.*,
			itemtype.name as type';
			$itemJoin = array(
				array('itemtype', 'itemtype.pkey=item.typekey', 'left'),
			);
			$item = $this->getDataRow('item', $itemSelect, '', '', $itemJoin); //item sementara

			//jika edit tambah itemsentara
			if ($_POST['action'] == 'update') {
				$itemDetail = $this->getDataRow('detail_itemout', '*', array('refkey' => $_POST['pkey']));
				foreach ($item as $key => $value) {
					//pengecekan data untuk di kembalikan sementara
					for ($i = 0; $i < count($itemDetail); $i++) {
						if ($itemDetail[$i]['itemkey'] == $value['pkey']) {
							$item[$key]['stock'] += $itemDetail[$i]['count'];
							break;
						}
					}
				}
				//pengcekan data kembali sesuai tidaknya
				foreach ($_POST['detailKey'] as $key => $value) {
					foreach ($itemDetail as $itemDetailKey => $itemDetailValue) {
						if ($_POST['itemKey'][$key] == $itemDetailValue['itemkey']) {
							if ($_POST['count'][$key] < $itemDetailValue['use']) {
								array_push($arrMsgErr, "Barang Kembali Tidak Sesuai");
								break;
							}
						}
					}
				}
			}



			//pengecekan stock sementara
			foreach ($_POST['itemKey'] as $key => $value) {
				for ($i = 0; $i < count($item); $i++) {
					if ($item[$i]['pkey'] == $_POST['itemKey'][$key]) {
						$item[$i]['stock'] -= $_POST['count'][$key];
						break;
					}
				}
			}

			//jika ada yg kurang kasi alert
			foreach ($item as $key => $value) {
				if ($value['stock'] < 0)
					array_push($arrMsgErr, "Stock " . $value['name'] . '_' . $value['type'] . ' Tidak Mencukupi');
			}
			$this->session->set_flashdata('arrMsgErr', $arrMsgErr);
			//validate form
			if (empty(count($arrMsgErr)))
				switch ($_POST['action']) {
					case 'add':
						$_POST['timedate'] = strtotime(date('d M Y'));
						foreach ($_POST['detailKey'] as $key => $value) {
							$_POST['timedateDetail'][$key] = strtotime(date('d M Y'));
						}

						$refkey = $this->insert($tableName, $this->dataForm($formData));
						$this->insertDetail($tableDetail, $formDetail, $refkey);
						foreach ($_POST['detailKey'] as $key => $value) {
							$this->query("UPDATE `item` SET `stock` = `stock` - " . $_POST['count'][$key] . " WHERE `pkey` =" . $_POST['itemKey'][$key]);
						}

						foreach ($_POST['workerKey'] as $key => $value) {
							$dataInsert = array(
								'refkey' => $refkey,
								'workerkey' => $value,
							);
							$this->insert($tableDetailTeknisi, $dataInsert);
						}
						//pembuatan logs
						$account = $this->getDataRow('account', '*', array('pkey' => $this->id))[0];
						$dataLogs = array(
							'name' => 'barang keluar',
							'log' => $account['name'] . ' menambah data barang keluar dengan id : ' . $refkey,
							'time' => strtotime('now'),
						);
						$this->insert('logs', $dataLogs);

						redirect(base_url($baseUrl . 'List')); //wajib terakhir
						break;
					case 'update':
						//mengembalikan stock 
						$dataDetail = $this->getDataRow('detail_itemout', '*', array('refkey' => $id));
						foreach ($dataDetail as $key => $value) {
							$this->query("UPDATE `item` SET `stock` = `stock` + " . $value['count'] . " WHERE `pkey` =" . $value['itemkey']);
						}
						$this->delete('detail_itemoutworker', array('refkey' => $id));
						//kurangi stock
						foreach ($_POST['detailKey'] as $key => $value) {
							$this->query("UPDATE `item` SET `stock` = `stock` - " . $_POST['count'][$key] . " WHERE `pkey` =" . $_POST['itemKey'][$key]);
						}
						foreach ($_POST['workerKey'] as $key => $value) {
							$dataInsert = array(
								'refkey' => $id,
								'workerkey' => $value,
							);
							$this->insert($tableDetailTeknisi, $dataInsert);
						}

						//pembuatan logs
						$account = $this->getDataRow('account', '*', array('pkey' => $this->id))[0];
						$dataLogs = array(
							'name' => 'barang keluar',
							'log' => $account['name'] . ' mengubah data barang keluar dengan id : ' . $id,
							'time' => strtotime('now'),
						);
						$this->insert('logs', $dataLogs);

						//ligacy update
						$this->update($tableName, $this->dataForm($formData), array('pkey' => $_POST['pkey']));
						$this->updateDetail($tableDetail, $formDetail, $detailRef, $id,);
						redirect(base_url($baseUrl . 'List'));
						break;
				}
		}

		if (!empty($id)) {
			$dataRow = $this->getDataRow($tableName, '*', array('pkey' => $id), 1)[0];
			$this->dataFormEdit($formData, $dataRow);
			$detailItem = $this->getDataRow('detail_itemout', '*', array('refkey' => $id));
			$detailWorker = $this->getDataRow('detail_itemoutworker', '*', array('refkey' => $id));

			$data['html']['detailWorker'] = $detailWorker;
			$data['html']['detailItem'] = $detailItem;
		}
		//item

		$itemSelect = $itemTable . '.*,
		itemtype.name itemtypename
		';
		$itemJoint = array(
			array('itemtype', 'itemtype.pkey=' . $itemTable . '.typekey', 'left'),
		);
		$item = $this->getDataRow('item', $itemSelect, '', '', $itemJoint);
		//item 

		$worker = $this->getDataRow('worker', 'pkey,name');
		$team = $this->getDataRow('team', 'pkey,name');

		$data['html']['team'] = $team;
		$data['html']['worker'] = $worker;
		$data['html']['item'] = $item;
		$data['html']['baseUrl'] = $baseUrl;
		$data['html']['title'] = 'Input Data Barang Keluar';
		$data['html']['err'] = $this->genrateErr();
		$data['url'] = 'admin/' . __FUNCTION__ . 'Form';
		$this->template($data);
	}

	public function workerList()
	{
		$tableName = 'worker';

		$join = array(
			array('account', 'account.pkey=' . $tableName . '.createon', 'left'),
			array('role', 'role.pkey=account.role', 'left'),

		);
		$select = '
			' . $tableName . '.*,
			account.name as createname,
			account.role as createrole,
			role.name as rolename,
		';

		$dataList = $this->getDataRow($tableName, $select, '', '', $join);
		$data['html']['title'] = 'List Teknisi';
		$data['html']['dataList'] = $dataList;
		$data['html']['tableName'] = $tableName;
		$data['html']['form'] = get_class($this) . '/worker';
		$data['url'] = 'admin/workerList';
		$this->template($data);
	}

	public function worker($id = '')
	{
		$tableName = 'worker';
		$tableDetail = '';
		$baseUrl = get_class($this) . '/' . __FUNCTION__;
		$detailRef = '';
		$formData = array(
			'pkey' => 'pkey',
			'name' => 'name',
			'createon' => 'sesionid',
			'time' => 'time',
		);
		$formDetail = array();
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if (empty($_POST['action'])) redirect(base_url($baseUrl . 'List'));
			//validate form
			$arrMsgErr = array();
			if (empty($_POST['name']))
				array_push($arrMsgErr, "Nama wajib Di isi");

			$this->session->set_flashdata('arrMsgErr', $arrMsgErr);
			//validate form
			if (empty(count($arrMsgErr)))
				switch ($_POST['action']) {
					case 'add':
						$refkey = $this->insert($tableName, $this->dataForm($formData));
						$this->insertDetail($tableDetail, $formDetail, $refkey);
						//tambah logs
						$account = $this->getDataRow('account', '*', array('pkey' => $this->id))[0];
						$logs = array(
							'name' => 'Teknisi',
							'log' => $account['name'] . ' Menambah teknisi ' . $_POST['name'],
							'time' => strtotime('now'),
						);
						$this->insert('logs', $logs);
						redirect(base_url($baseUrl . 'List')); //wajib terakhir
						break;
					case 'update':
						$this->update($tableName, $this->dataForm($formData), array('pkey' => $_POST['pkey']));
						$this->updateDetail($tableDetail, $formDetail, $detailRef, $id);
						//tambah logs
						$dataEdit = $this->getDataRow($tableName, 'name', array('pkey' => $_POST['pkey']))[0];
						$account = $this->getDataRow('account', '*', array('pkey' => $this->id))[0];
						$logs = array(
							'name' => 'Teknisi',
							'log' => $account['name'] . ' mengubah ' . $dataEdit['name'] . ' menjadi ' . $_POST['name'],
							'time' => strtotime('now'),
						);
						$this->insert('logs', $logs);

						redirect(base_url($baseUrl . 'List'));
						break;
				}
		}

		if (!empty($id)) {
			$dataRow = $this->getDataRow($tableName, '*', array('pkey' => $id), 1)[0];
			$this->dataFormEdit($formData, $dataRow);
		}



		$data['html']['baseUrl'] = $baseUrl;
		$data['html']['title'] = 'Input Teknisi';
		$data['html']['err'] = $this->genrateErr();
		$data['url'] = 'admin/' . __FUNCTION__ . 'Form';
		$this->template($data);
	}
	public function teamList()
	{
		$tableName = 'team';

		$join = array(
			array('account', 'account.pkey=' . $tableName . '.createon', 'left'),
			array('role', 'role.pkey=account.role', 'left'),

		);
		$select = '
			' . $tableName . '.*,
			account.name as createname,
			account.role as createrole,
			role.name as rolename,
		';

		$dataList = $this->getDataRow($tableName, $select, '', '', $join);
		$data['html']['title'] = 'List Team';
		$data['html']['dataList'] = $dataList;
		$data['html']['tableName'] = $tableName;
		$data['html']['form'] = get_class($this) . '/team';
		$data['url'] = 'admin/teamList';
		$this->template($data);
	}
	public function team($id = '')
	{
		$tableName = 'team';
		$tableDetail = '';
		$baseUrl = get_class($this) . '/' . __FUNCTION__;
		$detailRef = '';
		$formData = array(
			'pkey' => 'pkey',
			'name' => 'name',
			'createon' => 'sesionid',
			'time' => 'time',
		);
		$formDetail = array();
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if (empty($_POST['action'])) redirect(base_url($baseUrl . 'List'));
			//validate form
			$arrMsgErr = array();
			if (empty($_POST['name']))
				array_push($arrMsgErr, "Nama wajib Di isi");

			$this->session->set_flashdata('arrMsgErr', $arrMsgErr);
			//validate form
			if (empty(count($arrMsgErr)))
				switch ($_POST['action']) {
					case 'add':
						$refkey = $this->insert($tableName, $this->dataForm($formData));
						$this->insertDetail($tableDetail, $formDetail, $refkey);
						//tambah logs
						$account = $this->getDataRow('account', '*', array('pkey' => $this->id))[0];
						$logs = array(
							'name' => 'Team',
							'log' => $account['name'] . ' Menambah team ' . $_POST['name'],
							'time' => strtotime('now'),
						);
						$this->insert('logs', $logs);
						redirect(base_url($baseUrl . 'List')); //wajib terakhir
						break;
					case 'update':
						$this->update($tableName, $this->dataForm($formData), array('pkey' => $_POST['pkey']));
						$this->updateDetail($tableDetail, $formDetail, $detailRef, $id);
						//tambah logs
						$dataEdit = $this->getDataRow($tableName, 'name', array('pkey' => $_POST['pkey']))[0];
						$account = $this->getDataRow('account', '*', array('pkey' => $this->id))[0];
						$logs = array(
							'name' => 'Team',
							'log' => $account['name'] . ' mengubah Team ' . $dataEdit['name'] . ' menjadi ' . $_POST['name'],
							'time' => strtotime('now'),
						);
						$this->insert('logs', $logs);

						redirect(base_url($baseUrl . 'List'));
						break;
				}
		}

		if (!empty($id)) {
			$dataRow = $this->getDataRow($tableName, '*', array('pkey' => $id), 1)[0];
			$this->dataFormEdit($formData, $dataRow);
		}

		$data['html']['baseUrl'] = $baseUrl;
		$data['html']['title'] = 'Input Team';
		$data['html']['err'] = $this->genrateErr();
		$data['url'] = 'admin/' . __FUNCTION__ . 'Form';
		$this->template($data);
	}

	public function print($id)
	{
		$itemOutSelect = 'itemout.*,
		team.name teamname';
		$itemOutJoint = array(
			array('team', 'team.pkey=itemout.teamkey', 'left')
		);
		$itemOut = $this->getDataRow('itemout', $itemOutSelect, array('itemout.pkey' => $id), '', $itemOutJoint)[0];

		$detailSelect = 'detail_itemout.*,
		item.name as name,
		itemtype.name as type,
		';
		$detailJoin = array(
			array('item', 'item.pkey=detail_itemout.itemkey', 'left'),
			array('itemtype', 'itemtype.pkey=item.typekey', 'left'),
		);
		$itemDetail = $this->getDataRow('detail_itemout', $detailSelect, array('detail_itemout.refkey' => $itemOut['pkey']), '', $detailJoin);

		$joinWorker = array(
			array('worker', 'worker.pkey=detail_itemoutworker.workerkey', 'left')
		);
		$worker = $this->getDataRow('detail_itemoutworker', '*,worker.name as name', array('refkey' => $id), '', $joinWorker);

		$data['worker'] = $worker;
		$data['itemOut'] = $itemOut;
		$data['itemDetail'] = $itemDetail;

		$this->load->view('admin/itenOutPrint', $data);
	}
	public function returnList()
	{
		$tableName = 'itemout';

		$join = array(
			array('account', 'account.pkey=' . $tableName . '.returncreaton', 'left'),
			array('role', 'role.pkey=account.role', 'left'),

		);
		$select = '
			' . $tableName . '.*,
			account.name as createname,
			account.role as createrole,
			role.name as rolename,
		';

		$dataList = $this->getDataRow($tableName, $select, array('return' => 1), '', $join);
		$data['html']['title'] = 'List Return';
		$data['html']['dataList'] = $dataList;
		$data['html']['tableName'] = $tableName;
		$data['url'] = 'admin/returnList';
		$this->template($data);
	}


	public function closingList()
	{
		$tableName = 'close';

		$join = array(
			array('item', 'item.pkey=' . $tableName . '.itemkey', 'left'),
			array('itemtype', 'itemtype.pkey=item.typekey', 'left'),
		);
		$select = '
			' . $tableName . '.*,
			item.name as itemname,
			item.unitname as unitname,
			itemtype.name as typename,
		';
		$dataList = $this->getDataRow($tableName, $select, '', '', $join);
		$data['html']['title'] = 'List Closing';
		$data['html']['dataList'] = $dataList;
		$data['html']['tableName'] = $tableName;
		$data['url'] = 'admin/closingList';
		$this->template($data);
	}
	public function logsList()
	{
		$tableName = 'logs';

		$dataList = $this->getDataRow($tableName, '*');
		$data['html']['title'] = 'List Logs';
		$data['html']['dataList'] = $dataList;
		$data['html']['tableName'] = $tableName;
		// $data['html']['form'] = get_class($this) . '/itemOut';
		$data['url'] = 'admin/logsList';
		$this->template($data);
	}

	//export
	public function reportExport($start, $end)
	{
		$data = [];
		$estimasi = 86400;
		$stockAwal = $this->getDataRow('close', '*', array('time ' => (int)$start - $estimasi));

		//pencarian stock Awal
		if (count($stockAwal) == 0) {
			$reserch = 10;
			while ($reserch > 0) {
				$reserch--;
				$estimasi += 86400;
				$stockAwal = $this->getDataRow('close', '*', array('time ' => (int)$start - $estimasi));
				if (count($stockAwal) <> 0)
					break;
			}
		}

		//pencarian stock Akhir
		$estimasi = 0;
		$stockAkhir = $this->getDataRow('close', '*', array('time ' => (int)$end));
		if (count($stockAkhir) == 0) {
			$reserch = 10;
			while ($reserch > 0) {
				$reserch--;
				$estimasi += 86400;
				$stockAkhir = $this->getDataRow('close', '*', array('time ' => (int)$end - $estimasi));
				if (count($stockAkhir) <> 0)
					break;
			}
		}


		$itemInSelect = 'itemin.*,
		sum(count) as count,
		item.name as item,
		itemtype.name as type';
		$itemJoin = array(
			array('item', 'item.pkey=itemin.itemkey', 'left'),
			array('itemtype', 'itemtype.pkey=item.typekey', 'left'),
		);
		$itemIn = $this->getDataRow('itemin', $itemInSelect, array('timedate >=' => $start, 'timedate <=' => $end), '', $itemJoin, 'item.name asc', '', array('itemkey'));

		$selectItemOut = 'detail_itemout.*,
		sum(count) as count,
		';
		$itemOut = $this->getDataRow('detail_itemout', $selectItemOut, array('timedate >=' => $start, 'timedate <=' => $end), '', '', '', '', array('itemkey', 'timedate'));
		$itemUse = $this->getDataRow('detail_itemout', 'sum(`use`) as `use`,itemkey', array('timedate >=' => $start, 'timedate <=' => $end), '', '', '', '', array('itemkey'));


		$heder = ['no', 'Nama Barang', 'Stock Awal', 'Barang Masuk'];
		$index = ['number', 'itemname', 'stockAwal', 'itemIn'];


		$paramDate = [];
		foreach ($itemOut as $key => $value) {
			if (!in_array(date('d', $value['timedate']), $heder)) {
				array_push($heder, date('d', $value['timedate']));
				array_push($index, 'tgl_' . date('d', $value['timedate']));
				array_push($paramDate, date('d', $value['timedate']));
			}
		}

		//penambahan data =================================
		foreach ($itemIn as $itemInKey => $itemInVal) {
			$tempData = array(
				'itemkey' => $itemInVal['itemkey'],
				'itemname' => $itemInVal['item'] . '_' . $itemInVal['type'], //nama barang
				'itemIn' => $itemInVal['count'], //barang masuk
				'stockAwal' => 'Null',
			);

			//pencarian stock awal ====================
			foreach ($stockAwal as $stockAwalKey => $stockAwalVal) {
				if ($itemInVal['itemkey'] == $stockAwalVal['itemkey']) {
					$tempData['stockAwal'] = $stockAwalVal['laststock'];
				}
			}
			//pencarian data barang keluar
			foreach ($itemOut as $itemOutKey => $itemOutVal) {
				if ($itemOutVal['itemkey'] == $itemInVal['itemkey']) {
					for ($i = 0; $i < count($paramDate); $i++) {
						if ($paramDate[$i] == date('d', $itemOutVal['timedate'])) {
							$tempData['tgl_' . $paramDate[$i]] = $itemOutVal['count'];
						}
					}
				}
			}
			//penambahan barang terpakai
			foreach ($itemUse as $itemUseKey => $itemUseVal) {
				if ($itemUseVal['itemkey'] == $itemInVal['itemkey']) {
					$tempData['use'] = $itemUseVal['use'];
				}
			}

			//penambahan stock akhir
			foreach ($stockAkhir as $stockAkhirKey => $stockAkhirVal) {
				if ($itemInVal['itemkey'] == $stockAkhirVal['itemkey']) {
					$tempData['LastStock'] = $stockAkhirVal['laststock'];
				}
			}


			array_push($data, $tempData); //push ke Data untuk di export
		}

		array_push($heder, 'Brang Terpakai', 'Stock Akhir', 'Keterangan'); //menamhan barang terpakai
		array_push($index, 'use', 'LastStock'); //menamhan barang terpakai
		$this->exportExcel($heder, $index, $data, 'Report ' . date('d M', $start) . '_' . date('d M', $end));


		print_r($stockAkhir);
	}
	public function exportItemIn($start, $end)
	{

		$select = "itemin.*,
		CONCAT(item.name,'_',itemtype.name) as item,
		CONCAT(worker.name,'_',team.name) as sumpiler,

		";
		$join = array(
			array('item', 'item.pkey=itemin.itemkey', 'left'),
			array('itemtype', 'itemtype.pkey=item.typekey', 'left'),
			array('worker', 'worker.pkey=itemin.workerkey', 'left'),
			array('team', 'team.pkey=itemin.teamkey', 'left'),
		);
		$data = $this->getDataRow('itemin', $select, array('timedate >=' => $start, 'timedate <=' => $end), '', $join);
		foreach ($data as $key => $value) {
			$data[$key]['hour'] = date('H:i', $value['time']);
		}

		$heder = ['No', 'Barang', 'Jumlah', 'Tanggal', 'Jam', 'Supiler'];
		$index = ['number', 'item', 'count', ['timedate', 'time'], 'hour', 'sumpiler'];
		$this->exportExcel($heder, $index, $data, 'Report Barang masuk ' . date('d M', $start) . '_' . date('d M', $end));
	}
	public function exportClose($start, $end)
	{

		$select = "close.*,
		CONCAT(item.name,'_',itemtype.name) as item,
		";
		$join = array(
			array('item', 'item.pkey=close.itemkey', 'left'),
			array('itemtype', 'itemtype.pkey=item.typekey', 'left'),
		);
		$data = $this->getDataRow('close', $select, array('close.time >=' => $start, 'close.time <=' => $end), '', $join);

		$heder = ['No', 'Barang', 'Jumlah', 'Tanggal',];
		$index = ['number', 'item', 'laststock', ['time', 'time']];
		$this->exportExcel($heder, $index, $data, 'Report Closing ' . date('d M', $start) . '_' . date('d M', $end));
	}

	public function userList()
	{
		if ($this->session->userdata('role') != '1')
			redirect(base_url());
		$tableName = 'account';
		$join = array(
			array('role', 'role.pkey=account.role', 'left'),
		);
		$dataList = $this->getDataRow($tableName, 'account.*, role.name as rolename', '', '', $join, 'name ASC');
		$data['html']['title'] = 'List Account';
		$data['html']['dataList'] = $dataList;
		$data['html']['tableName'] = $tableName;
		$data['html']['form'] = get_class($this) . '/user';
		$data['url'] = 'admin/userList';
		$this->template($data);
	}

	public function user($id = '')
	{
		$tableName = 'account';
		$tableDetail = '';
		$baseUrl = get_class($this) . '/' . __FUNCTION__;
		$detailRef = '';
		$formData = array(
			'pkey' => 'pkey',
			'name' => 'name',
			'username' => 'username',
			'role' => 'role',
		);
		$formDetail = array();

		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if (empty($_POST['action'])) redirect(base_url($baseUrl . 'List'));
			//validate form
			$arrMsgErr = array();
			if (empty($_POST['name']))
				array_push($arrMsgErr, "Password wajib Di isi");

			if (empty($_POST['password']) && $_POST['action'] == 'add')
				array_push($arrMsgErr, "Password wajib Di isi");
			if ($_POST['role'] == '1')
				unset($_POST['detailKey']);



			$this->session->set_flashdata('arrMsgErr', $arrMsgErr);
			//validate form
			if (empty(count($arrMsgErr)))
				switch ($_POST['action']) {
					case 'add':
						$formData['password'] = array('password', 'md5');
						$refkey = $this->insert($tableName, $this->dataForm($formData));
						$this->insertDetail($tableDetail, $formDetail, $refkey);
						redirect(base_url($baseUrl . 'List')); //wajib terakhir
						break;
					case 'update':
						if (!empty($_POST['password']))
							$formData['password'] = array('password', 'md5');
						$this->update($tableName, $this->dataForm($formData), array('pkey' => $_POST['pkey']));
						$this->updateDetail($tableDetail, $formDetail, $detailRef, $id);
						redirect(base_url($baseUrl . 'List'));
						break;
				}
		}

		if (!empty($id)) {
			$dataRow = $this->getDataRow($tableName, '*', array('pkey' => $id), 1)[0];
			$this->dataFormEdit($formData, $dataRow);
			$_POST['password'] = '';
		}
		$selVal = $this->getDataRow('role', '*', '', '', '', 'name ASC');
		$selValClass = $this->getDataRow('class', '*', '', '', '', 'name ASC');

		$data['html']['selValClass'] = $selValClass;
		$data['html']['baseUrl'] = $baseUrl;
		$data['html']['selVal'] = $selVal;
		$data['html']['title'] = 'Input Data ' . __FUNCTION__;
		$data['html']['err'] = $this->genrateErr();
		$data['url'] = 'admin/' . __FUNCTION__ . 'Form';
		$this->template($data);
	}

	public function ajax()
	{
		if (empty($_POST['action'])) {
			echo 'no action';
			die;
		}
		switch ($_POST['action']) {
			case 'delete':
				switch ($_POST['tbl']) {
					case 'itemin':
						$itemIn = $this->getDataRow($_POST['tbl'], '*', array('pkey' => $_POST['pkey']))[0];
						if ($itemIn['status'] == 1)
							die;
						$item = $this->getDataRow('item', 'stock,name,typekey', array('pkey' => $itemIn['itemkey']))[0];
						$itemType = $this->getDataRow('itemtype', 'name', array('pkey' => $item['typekey']))[0];
						//pembuatan log 
						$account = $this->getDataRow('account', 'name', array('pkey' => $this->id))[0];
						$logs = array(
							'name' => 'barang masuk',
							'log' => $account['name'] . ' melakukan penghapusan barang masuk dengan data : ' . $item['name'] . '_' . $itemType['name'] . ' jumlah_barang : ' . $itemIn['count'] . ' Catatan :' . $itemIn['note'] . ' Tanggal : ' . date('d/m/Y H:i', $itemIn['time']),
							'time' => strtotime('now'),
						);
						$this->insert('logs', $logs);
						$item['stock'] = $item['stock'] - $itemIn['count'];
						$this->update('item', array('stock' => $item['stock']), array('pkey' => $itemIn['itemkey']));
						$this->delete($_POST['tbl'], 'pkey=' . $_POST['pkey']);
						break;
					case 'itemout':
						//pengambilan data
						$select = $_POST['tbl'] . '.*,
						account.name accountname,
						team.name teamname,
						';
						$join = array(
							array('account', 'account.pkey=' . $_POST['tbl'] . '.createon', 'left'),
							array('team', 'team.pkey=' . $_POST['tbl'] . '.teamkey', 'left'),
						);
						$data = $this->getDataRow($_POST['tbl'], $select, array($_POST['tbl'] . '.pkey' => $_POST['pkey']), '', $join)[0];
						//data item
						$itemJin = array(
							array('item', 'item.pkey=detail_itemout.itemkey', 'left'),
							array('itemtype', 'itemtype.pkey=item.typekey', 'left')
						);
						$detaiItemSelect = 'detail_itemout.*,
						item.name as itemname,
						item.unitname as unitname,
						itemtype.name as typename,
						';
						$detailItem = $this->getDataRow('detail_itemout', $detaiItemSelect, array('refkey' => $_POST['pkey']), '', $itemJin);
						//data worker
						$workerJoin = array(
							array('worker', 'worker.pkey=detail_itemoutworker.workerkey', 'left')
						);
						$detailWorker = $this->getDataRow('detail_itemoutworker', 'detail_itemoutworker.*,worker.name name', array('refkey' => $_POST['pkey']), '', $workerJoin);

						//pembuatan logs
						$log = $data['accountname'] . ' menghapus data  \r\n';
						$log .= 'team :' . $data['teamname'] . '  \r\n';
						$log .= 'note :' . $data['note'] . '  \r\n';
						$log .= 'Barang : \r\n';
						foreach ($detailItem as $key => $value) {
							$log .= $value['itemname'] . '_' . $value['typename'] . ': ' . $value['count'] . ' ' .  $value['unitname'] . ' \r\n';
						}
						$log .= 'Teknisi : \r\n';
						foreach ($detailWorker as $key => $value) {
							$log .=  $value['name'] . ' \r\n';
						}

						$logs = array(
							'name' => 'barang keluar',
							'time' => strtotime('now'),
							'log' => $log,
						);
						$this->insert('logs', $logs);

						//mengembalikan stock
						foreach ($detailItem as $key => $value) {
							$this->query("UPDATE `item` SET `stock` = `stock` + " . $value['count'] . " WHERE `pkey` =" . $value['itemkey']);
						}

						//delete semua
						$this->delete($_POST['tbl'], 'pkey=' . $_POST['pkey']);
						$this->delete('detail_itemout', array('refkey' => $_POST['pkey']));
						$this->delete('detail_itemoutworker', array('refkey' => $_POST['pkey']));


						break;
					default:
						$this->delete($_POST['tbl'], 'pkey=' . $_POST['pkey']);
						break;
				}
				break;
			case 'statusItemType':
				$checkdata = $this->getDataRow('itemtype', 'status', array('pkey' => $_POST['pkey']))[0];
				$status = 1;
				if ($checkdata['status'] == 1)
					$status = 0;
				$this->update('itemtype', array('status' => $status), array('pkey' => $_POST['pkey']));
				break;

				$oldststus = $this->getDataRow('content', 'status', array('pkey' => $_POST['pkey']));
				$status = '1';
				if ($oldststus[0]['status'] == '1')
					$status = '0';
				$this->update('content', array('status' => $status), array('pkey' => $_POST['pkey']));
				break;
			case 'addReturn':
				$status = 'success';
				$dataSelect = 'itemout.*,
				team.name as teamname';
				$dataJoin = array(
					array('team', 'team.pkey=itemout.teamkey', 'left')
				);
				$whereDta = array('itemout.pkey' => $_POST['id'], 'return' => 0);
				if (isset($_POST['param']) && $_POST['param'] == 'edit')
					$whereDta = array('itemout.pkey' => $_POST['id']);
				$data = $this->getDataRow('itemout', $dataSelect, $whereDta, '', $dataJoin);
				$itemSelect = 'detail_itemout.*,
				item.name as itemname,
				item.unitname as unitname,
				itemtype.name as itemtype,
				';
				$itemJoin = array(
					array('item', 'item.pkey=detail_itemout.itemkey', 'left'),
					array('itemtype', 'itemtype.pkey=item.typekey', 'left')
				);
				$itemDetail = $this->getDataRow('detail_itemout', $itemSelect, array('detail_itemout.refkey' => $_POST['id']), '', $itemJoin);
				$workerJoin = array(
					array('worker', 'worker.pkey=detail_itemoutworker.workerkey', 'left')
				);
				$worker = $this->getDataRow('detail_itemoutworker', 'detail_itemoutworker.*,worker.name as name', array('refkey' => $_POST['id']), '', $workerJoin);
				if (count($data) == 0) {
					$status = 'Data Tidak Di temukan';
				} else {
					$data = $data[0];
				}
				echo json_encode(['status' => $status, 'data' => $data, 'detail' => $itemDetail, 'worker' => $worker]);
				break;
			case 'submitReturn':
				$status = 'success';

				$itemDetail = $this->getDataRow('detail_itemout', '*', array('refkey' => $_POST['pkey']));
				//validasi

				for ($i = 0; $i < count($_POST['detailkey']); $i++) {
					foreach ($itemDetail as $key => $value) {
						if ($value['pkey'] == $_POST['detailkey'][$i]) {
							if ($value['count'] < $_POST['return'][$i] || $_POST['return'][$i] < 0) {
								echo json_encode(['status' => 'Barang Terpakai Tidak Sesuai']);
								die;
							}
						}
					}
				}
				//pengimputan data jika add
				for ($i = 0; $i < count($_POST['detailkey']); $i++) {
					foreach ($itemDetail as $key => $value) {
						if ($value['pkey'] == $_POST['detailkey'][$i]) {
							if ($_POST['subaction'] == 'edit') {
								$this->set('item', array('pkey' => $value['itemkey']), array('stock', '`stock` - ' . $value['countreturn'], false));
							}

							$use = $value['count'] - $_POST['return'][$i];
							$this->update('detail_itemout', array('countreturn' => $_POST['return'][$i], 'use' => $use, 'note' => $_POST['note'][$i]), array('pkey' => $_POST['detailkey'][$i]));

							$this->set('item', array('pkey' => $value['itemkey']), array('stock', '`stock` + ' . $_POST['return'][$i], false));
						}
					}
				}

				$this->update('itemout', array('return' => 1, 'returntime' => strtotime('now'), 'returncreaton' => $this->id), array('pkey' => $_POST['pkey']));
				echo json_encode(['status' => $status]);
				break;
			case 'close';
				//close item
				$item = $this->getDataRow('item', 'pkey,stock');
				foreach ($item as $itemKey => $itemValue) {
					$closeItem = array(
						'itemkey' => $itemValue['pkey'],
						'time' => strtotime(date('d/m/Y H:i')),
						'laststock' => $itemValue['stock'],
					);
					$this->insert('close', $closeItem);
				}
				$this->update('itemin', array('status' => 1), array('status' => 0));
				$this->update('itemout', array('status' => 1), array('status' => 0));
				break;
			case 'export':
				$status = 'success';
				$url = '';
				$startDate = strtotime($_POST['start']);
				$endDate = strtotime($_POST['end']);
				if ($startDate > $endDate) {
					$status = 'Tanggal Mulai Lebih Tidak Boleh Lebih Besar';
				} else {
					$url = base_url('Admin/reportExport/' . $startDate . '/' . $endDate);
				}
				echo json_encode(['status' => $status, 'url' => $url]);
				break;
			case 'exportItemIn':
				$status = 'success';
				$url = '';
				$startDate = strtotime($_POST['start']);
				$endDate = strtotime($_POST['end']);
				if ($startDate > $endDate) {
					$status = 'Tanggal Mulai Lebih Tidak Boleh Lebih Besar';
				} else {
					$url = base_url('Admin/exportItemIn/' . $startDate . '/' . $endDate);
				}
				echo json_encode(['status' => $status, 'url' => $url]);
				break;
			case 'exportClose':
				$status = 'success';
				$url = '';
				$startDate = strtotime($_POST['start']);
				$endDate = strtotime($_POST['end']);
				if ($startDate > $endDate) {
					$status = 'Tanggal Mulai Lebih Tidak Boleh Lebih Besar';
				} else {
					$url = base_url('Admin/exportClose/' . $startDate . '/' . $endDate);
				}
				echo json_encode(['status' => $status, 'url' => $url]);
				break;
			default:
				echo 'action is not in the list';
				break;
		}
	}
}
