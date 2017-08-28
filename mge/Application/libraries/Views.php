<?php
/**
 * @author yangf@songplay.cn
 * @date 2016-5-4
 * 绘制view层HTML代码
 */

class Views{
	
	// 表格导出插件下载地址
	const DOWN_TOOL_URL = 'http://119.28.19.213:8004/copy_csv_xls_pdf.swf';
	
	private $CI;
	
	/**
	 * html文本
	 * @var unknown
	 */
	private $html = '';
	
	/**
	 * 页面唯一id
	 * @var unknown
	 */	
	private $tab_id = '';
	
	/**
	 * 节点唯一标识
	 * @var unknown
	 */
	private $node_id = '';
	
	/**
	 * 额外面板
	 * @var unknown
	 */
	private $model = '';
	
	/**
	 * 额外面板
	 * @var unknown
	 */
	private $js = '';
	
	/**
	 * 行编号
	 * @var unknown
	 */
	private static $row_num = 0;
	
	
	public function __construct(array $tab){
		// 参数监测
		if (isset($tab[0]) && isset($tab[1]) && isset($tab[2]) && isset($tab[3])) {
			$this->file = $tab[0];
			$this->controller = $tab[1];
			$this->method = $tab[2];
			$this->node_id = $tab[3];
			
			$this->ajaxTableUrl = base_url().'index.php/' . $this->file . '/' . $this->controller . '/ajaxTable';
			$this->ajaxDataUrl = base_url().'index.php/' . $this->file . '/' . $this->controller . '/ajaxData';
			// 编辑model的uri
			$this->editUrl = base_url().'index.php/' . $this->file . '/' . $this->controller . '/edit';
			$this->addUrl = base_url().'index.php/' . $this->file . '/' . $this->controller . '/add';
			$this->remoteUrl = base_url().'index.php/' . $this->file . '/' . $this->controller . '/remote';
			$this->deleteUrl = base_url().'index.php/' . $this->file . '/' . $this->controller . '/delete';
            $this->deleteUrl1 = base_url().'index.php/' . $this->file . '/' . $this->controller . '/delete1';
            $this->deleteUrl2 = base_url().'index.php/' . $this->file . '/' . $this->controller . '/delete2';
			$this->baseUrl = base_url().'index.php/' . $this->file . '/' . $this->controller . '/';
			$this->table_id = 'table_' . $this->node_id;
		}else{
			execExit('views类构造失败');
		}
		$this->CI =& get_instance();
		
		// 全部行内容
		$this->sbody = '';
		$this->tab_id = 'tab_' . $this->node_id;
	}
	
	public function setRow($row){
		
		// 行id标识
		self::$row_num ++;
		$row_id = 'row_' . $this->node_id . '_' . self::$row_num;

		// 行内容生成
		$body = '';
		foreach ($row as $key => $value) {
						$type = isset($value['type']) ? $value['type'] : 'text';
						// 内容标识
						$name = isset($value['name']) ? $value['name'] : '';
						// 描述
						$desc = isset($value['desc']) ? $value['desc'] : '';
						// 长度
						$col = isset($value['col']) ? 'col-sm-' . $value['col'] : 'col-sm-2';
				
						$maxlength = isset($value['maxlength']) ? 'maxlength="' . $value['maxlength'] . '"'  : '';
							
						switch ($type) {
							// 新增输入框
							case 'text':
								$val = isset($value['val']) ? 'value="' . $value['val'] .'"' : '';
								
								$body .= <<<search
									<div class="$col">
										<input name="$name" class="form-control" placeholder="$desc" type="text" $maxlength $val>
									</div>
search;
								break;
							case 'sedate':
								$sDate = $this->CI->config->item('searchDate')['sDate'];
								$eDate = $this->CI->config->item('searchDate')['eDate'];
								
								$body .= <<<search
									<div class="col-sm-2">
										<div class="input-group date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-field="" data-link-format="yyyy-mm-dd">
							                <input class="form-control" size="16" type="text" value="$sDate" name="sDate">
							                <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
											<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
							            </div>
							        </div>
										
									<div class="col-sm-2">
										<div class="input-group date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-field="" data-link-format="yyyy-mm-dd">
											<input class="form-control" size="16" type="text" value="$eDate" name="eDate">
											<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
											<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
										</div>
									</div>
									<script type="text/javascript">
							            $('.form_date').datetimepicker({
							                language:  'zh-CN',
							                weekStart: 1,
							                todayBtn:  1,
								       		autoclose: 1,
								       		todayHighlight: 1,
								       		startView: 2,
								       		minView: 2,
								       		forceParse: 0,
							            });
									</script>
search;
							break;
							case 'date':
									$date = date('Y-m-d');
								
									$body .= <<<search
									<div class="col-sm-2">
										<div class="input-group date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-field="" data-link-format="yyyy-mm-dd">
							                <input class="form-control" size="16" type="text" value="$date" name="date">
							                <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
											<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
							            </div>
							        </div>
									<script type="text/javascript">
							            $('.form_date').datetimepicker({
							                language:  'zh-CN',
							                weekStart: 1,
							                todayBtn:  1,
								       		autoclose: 1,
								       		todayHighlight: 1,
								       		startView: 2,
								       		minView: 2,
								       		forceParse: 0,
							            });
									</script>
search;
							break;
							case 'time':
								$date = isset($value['def']) ? $value['def'] : date('H:i');
							
								$body .= <<<search
							<div class="col-sm-2">
								<div class="input-group date form_time" data-date="" data-date-format="hh:ii" data-link-field="" data-link-format="hh:ii">
					                <input class="form-control" size="16" type="text" value="$date" name="$name">
					                <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
									<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
					            </div>
					        </div>
							<script type="text/javascript">
					            $('.form_time').datetimepicker({
					                language:  'zh-CN',
							        weekStart: 1,
							        todayBtn:  1,
									autoclose: 1,
									todayHighlight: 1,
									startView: 1,
									minView: 0,
									maxView: 1,
									forceParse: 0
					            });
							</script>
search;
							break;
							case 'select':
								$list = isset($value['list']) ? $value['list'] : array();
								
								$onchange = isset($value['onchange']) ? 'onchange="' . $value['onchange'] . '" ' : '';
				
								$options = '';
								foreach ($list as $key3 => $value3) {
									$options .= "<option value=" . $key3 . ">" . $value3 . "</option>";
								}
				
								$body .= <<<search
									<div class="$col">
										<select name="$name" class="form-control" $onchange>
											$options
										</select>
									</div>
search;
								break;
							case 'password':
								$body .= <<<search
									<div class="$col">
										<input name="$name" class="form-control" placeholder="$desc" type="password">
									</div>
search;
								break;
							case 'button':
								$button = isset($value['onclick']) ? $value['onclick'] : '';
								// 某些在选中数据后打开的mod不需要导入的列
								$noInsert = isset($value['noInsert']) && is_array($value['noInsert']) ? implode(',', $value['noInsert']) : '';
								
								$onclick = '';
								if (is_array($button)) {
									$type = isset($button['type']) ? $button['type'] : '';
									switch ($type) {
										case 'ajax':
											$onclick = 'onclick="doAjax(\'' . $this->ajaxDataUrl . '\', \'' . $this->tab_id .'\')" ';
											break;
										case 'reload':
											$desc = $desc == '' ? '查询' : $desc;
											$onclick = 'onclick="table_reload(\'' . $this->table_id .'\', \'' . $this->tab_id . '\')" ';
										break;
										case 'edit':
											$edit_model_id = $this->table_id . '_edit';
											$field = isset($button['field']) ? $button['field'] : array();
											$edit_validators_code = '';
											
											$onclick = 'onclick="edit_i_data(\'' . $edit_model_id . '\', \'' . $this->table_id . '\', \'' . $noInsert . '\')" data-toggle="modal" data-target="#' . $edit_model_id . '"';
											$this->model .= <<<str
												<div class="modal fade" id="$edit_model_id" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												    <div class="modal-dialog">
												        <div class="modal-content">
												            <div class="modal-header">
												                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
												                <h4 class="modal-title" id="myModalLabel">$desc</h4>
												            </div>
												            <div class="modal-body">
str;
											foreach ($field as $key4 => $value4) {
												$type_field_edit = isset($value4['type']) ? $value4['type'] : 'text';
												$name_field_edit = isset($value4['name']) ? $value4['name'] : '';
												$desc_field_edit = isset($value4['desc']) ? $value4['desc'] : '';
												$validator_edit = isset($value4['validator']) ? $value4['validator'] : null;
												
												$show_code = isset($value4['show']) && $value4['show'] == false  ? 'style="display: none"' : '';
												
												if (isset($validator_edit)) {
													$validator_types_edit = isset($validator_edit['type']) ? $validator_edit['type'] : 'notEmpty';
													// $error_msg_add = isset($validator_add['errorMsg']) ? $validator_add['errorMsg'] : '数据异常';
														
													// 分解验证类型(可能存在多个验证)
													$validator_type_edit = explode(',', $validator_types_edit);
												
													$validator_type_edit_code = '';
														
														
													if (isset($validator_edit['maxStr'])) {
														$maxStr = $validator_edit['maxStr'];
														$minStr = isset($validator_edit['minStr']) ? $validator_edit['minStr'] : 0;
												
														$validator_type_edit_code .= <<<str
										                    stringLength: {
										                        min: $minStr,
										                        max: $maxStr,
										                        message: '输入框只支持 $minStr~$maxStr 位的字符'
										                    },
str;
													}
														
													foreach ($validator_type_edit as $value5) {
												
												
														switch ($value5) {
															case 'notEmpty':
																$validator_type_edit_code .= <<<str
																	notEmpty:{
																		message:'输入框不允许为空!'
																	},
									
str;
																break;
																case 'minS':
																$validator_type_edit_code .= <<<str
																regexp: {
											                        regexp: /^[a-zA-Z0-9_]+$/,
											                        message: 'The username can only consist of alphabetical, number, dot and underscore'
											                    },
str;
															break;
														case 'remote':
															$validator_type_edit_code .= <<<str
											                    remote: {
											                        url: '$this->remoteUrl',
											                        message: '数据不合法,请重新输入',
											                    },
str;
														break;
 														case 'int':
 															// 取出区间
 															if (isset($validator_edit['min'])) {
 																$min = $validator_edit['min'];
 								
																$validator_type_edit_code .= <<<str
												                    greaterThan: {
												                        value: $min,
												                        inclusive: false,
												                        message: '值不能小于$min'
												                    },
str;
 															}
												
 															if (isset($validator_edit['max'])) {
 																$max = $validator_edit['max'];
												
 																$validator_type_edit_code .= <<<str
											 						lessThan: {
												                        value: $max,
												                        inclusive: true,
												                        message: '值不能大于$max'
												                    },
str;
 															}
														default:
															continue;
															break;
														}
													}
							
													$edit_validators_code .= <<<str
																$name_field_edit : {
																	validators: {
																		$validator_type_edit_code
																	}
									
																},
str;
												}
												
												
												switch ($type_field_edit) {
													case 'text':
														$code = isset($value4['value']) ? 'value="' . $value4['value'] . '"' : '';
														
														$this->model .= <<<str
														<div class="form-group">
										            		<input class="form-control" placeholder="$desc_field_edit" name="$name_field_edit" type="$type_field_edit" $code>
										            	</div>
str;
													break;
													case 'select':
														$list = isset($value4['list']) ? $value4['list'] : array();
													
														$onchange = isset($value['onchange']) ? 'onchange="' . $value['onchange'] . '" ' : '';
													
														$options = '';
														foreach ($list as $key3 => $value3) {
															$options .= "<option value=" . $key3 . ">" . $value3 . "</option>";
														}
													
														$this->model .= <<<search
															<div class="form-group">
																<select name="$name_field_edit" class="form-control" $onchange>
																	$options
																</select>
															</div>
search;
													break;
														case 'checkbox';
														$list = isset($value4['list']) ? $value4['list'] : array();
															
														$options = '';
														foreach ($list as $key3 => $value3) {
															$options .= '<label class="checkbox-inline"><input type="checkbox" name="' . $name_field_edit . '" value="' . $key3 . '">' . $value3 . '</label>';
														}
														
														
														$this->model .= <<<search
															<div class="form-group">
																<label>$desc_field_edit</label>
																$options
															</div>
search;
														break;
														case 'textarea';
														$this->model .= <<<str
															<div class="form-group">
																<textarea class="form-control" placeholder="$desc_field_edit" rows="3" name="$name_field_edit"></textarea>
											            	</div>
str;
														break;
													default:
														;
													break;
												}
												
											}
											
											$this->model .= <<<str
												            </div>
																									
												            <div class="modal-footer">
												                <button type="button" class="btn btn-primary" data-dismiss="model" onclick="edit('$this->editUrl', '$edit_model_id', '$this->table_id')"  >确定</button>
												            </div>
												        </div>
												        <!-- /.modal-content -->
												    </div>
												    <!-- /.modal-dialog -->
												</div>	
											<script>
													$('#$edit_model_id').bootstrapValidator({
												        message: 'This value is not valid',
												        feedbackIcons: {
												            valid: 'glyphicon glyphicon-ok',
												            invalid: 'glyphicon glyphicon-remove',
												            validating: 'glyphicon glyphicon-refresh'
												        },
												        trigger: 'blur',
												  		fields: {
															$edit_validators_code
													    }
													});
											</script>
str;
										break;
										case 'delete':
											$onclick = 'onclick="delRow(\'' . $this->deleteUrl . '\', \'' . $this->table_id .'\')" ';
										break;
                                        case 'delete1':
                                            $onclick = 'onclick="delRow(\'' . $this->deleteUrl1 . '\', \'' . $this->table_id .'\')" ';
                                            break;
                                        case 'delete2':
                                            $onclick = 'onclick="delRow(\'' . $this->deleteUrl2 . '\', \'' . $this->table_id .'\')" ';
                                            break;
										case 'div':
											$modId = isset($button['modId']) ? $button['modId'] : '';
											$onclick = 'data-toggle="modal" data-target="#' . $modId . '"';
										break;
										case 'add':
											$this->add_model_id = $add_model_id = $this->table_id . '_add';
											$field = isset($button['field']) ? $button['field'] : array();
											$add_validators_code = '';
											$this->add_model_edit_table_id = $this->tab_id . '_add_model_edit_table';
											
											$onclick = 'data-toggle="modal" onclick="edit_i_data(\''.$add_model_id .'\',\''. $this->table_id . '\', \'' . $noInsert . '\')" data-target="#' . $add_model_id . '"';
											$this->model .= <<<str
												<div class="modal fade" id="$add_model_id" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												    <div class="modal-dialog">
												        <div class="modal-content">
												            <div class="modal-header">
												                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
												                <h4 class="modal-title" id="myModalLabel">$desc</h4>
												            </div>
												            <div class="modal-body">
str;
											foreach ($field as $key4 => $value4) {
												$type_field_edit = isset($value4['type']) ? $value4['type'] : 'text';
												$name_field_edit = isset($value4['name']) ? $value4['name'] : '';
												$desc_field_edit = isset($value4['desc']) ? $value4['desc'] : '';
												$validator_add = isset($value4['validator']) ? $value4['validator'] : null;
												
												$show_code = isset($value4['show']) && $value4['show'] == false  ? 'style="display: none"' : '';
												
												if (isset($validator_add)) {
													$validator_types_add = isset($validator_add['type']) ? $validator_add['type'] : 'notEmpty';
													// $error_msg_add = isset($validator_add['errorMsg']) ? $validator_add['errorMsg'] : '数据异常';
													
													// 分解验证类型(可能存在多个验证)
													$validator_type_add = explode(',', $validator_types_add);

													$validator_type_add_code = '';
													
													
													if (isset($validator_add['maxStr'])) {
														$maxStr = $validator_add['maxStr'];
														$minStr = isset($validator_add['minStr']) ? $validator_add['minStr'] : 0;
														
														$validator_type_add_code .= <<<str
										                    stringLength: {
										                        min: $minStr,
										                        max: $maxStr,
										                        message: '输入框只支持 $minStr~$maxStr 位的字符'
										                    },
str;
													}
													
													foreach ($validator_type_add as $value5) {
														
														
														switch ($value5) {
														case 'notEmpty':
															$validator_type_add_code .= <<<str
																	notEmpty:{
																		message:'输入框不允许为空!'
																	},
																	
str;
															break;														
															case 'minS':
															$validator_type_add_code .= <<<str
											                    regexp: {
											                        regexp: /^[a-zA-Z0-9_]+$/,
											                        message: 'The username can only consist of alphabetical, number, dot and underscore'
											                    },
str;
															break;
														case 'remote':
															$validator_type_add_code .= <<<str
											                    remote: {
											                        url: '$this->remoteUrl',
											                        message: '数据不合法,请重新输入',
											                    },
str;
														break;
 														case 'int':
 															// 取出区间
 															if (isset($validator_add['min'])) {
 																$min = $validator_add['min'];
 																
																$validator_type_add_code .= <<<str
												                    greaterThan: {
												                        value: $min,
												                        inclusive: false,
												                        message: '值不能小于$min'
												                    },
str;
 															}
 															
 															if (isset($validator_add['max'])) {
 																$max = $validator_add['max'];
 																	
 																$validator_type_add_code .= <<<str
											 						lessThan: {
												                        value: $max,
												                        inclusive: true,
												                        message: '值不能大于$max'
												                    },
str;
 															}
														default:
															continue;
															break;
														}
													}
													
													$add_validators_code .= <<<str
																$name_field_edit : {
																	validators: {
																		$validator_type_add_code
																	}
																	
																},
str;
												}
										
												switch ($type_field_edit) {
													case 'text':
														$code = isset($value4['value']) ? 'value="' . $value4['value'] . '"' : '';
														
														$this->model .= <<<str
														<div class="form-group" $show_code>
										            		<input class="form-control" placeholder="$desc_field_edit" name="$name_field_edit" type="$type_field_edit" $code>
										            	</div>
str;
														break;
													case 'select':
														$list = isset($value4['list']) ? $value4['list'] : array();
															
														$onchange = isset($value['onchange']) ? 'onchange="' . $value['onchange'] . '" ' : '';
															
														$options = '';
														foreach ($list as $key3 => $value3) {
															$options .= "<option value=" . $key3 . ">" . $value3 . "</option>";
														}
															
														$this->model .= <<<search
															<div class="form-group">
																<select name="$name_field_edit" class="form-control" $onchange>
																	$options
																</select>
															</div>
search;
														break;
													case 'checkbox':
														$list = isset($value4['list']) ? $value4['list'] : array();
															
														$options = '';
														foreach ($list as $key3 => $value3) {
															$options .= '<label class="checkbox-inline"><input type="checkbox" name="' . $name_field_edit . '" value="' . $key3 . '">' . $value3 . '</label>';
														}

														
														$this->model .= <<<search
															<div class="form-group">
																<label>$desc_field_edit</label>
																$options
															</div>
search;
														break;
														case 'textarea';
														$this->model .= <<<str
															<div class="form-group">
																<textarea class="form-control" placeholder="$desc_field_edit" rows="3" name="$name_field_edit"></textarea>
											            	</div>
str;
														break;
													break;
													case 'editTable': 
														$thList = isset($value4['thList']) ? $value4['thList'] : array();
														$this->setEditTable('model', $thList);
													break;
													default:
														;
														break;
												}
										
											}
												
											$this->model .= <<<str
												            </div>
													
												            <div class="modal-footer">
												                <button type="button" class="btn btn-primary" data-dismiss="model" onclick="addModel('$add_model_id', '$this->table_id', '$this->addUrl')"  >确定</button>
												            </div>
												        </div>
												        <!-- /.modal-content -->
												    </div>
												    <!-- /.modal-dialog -->
												</div>
											<script>
													$('#$add_model_id').bootstrapValidator({
												        message: 'This value is not valid',
												        feedbackIcons: {
												            valid: 'glyphicon glyphicon-ok',
												            invalid: 'glyphicon glyphicon-remove',
												            validating: 'glyphicon glyphicon-refresh'
												        },
												        trigger: 'blur',
												  		fields: {
															$add_validators_code
													    }
													});
						
											</script>
str;
											break;										
										default:
											continue;
											break;
									}
								}
				
				
								$body .= <<<search
									<div class="$col">
										<button class="btn btn-outline btn-default" $onclick>$desc</button>
									</div>
search;
								break;
							case 'table':
								$thList = isset($value['thList']) ? $value['thList'] : array();
								$colList = isset($value['colList']) ? $value['colList'] : array();
								
								
								// 表行
								$thStr = '';
								foreach ($thList as $value) {
									$thStr .= '<th>'.$value.'</th>';
								}
								
								// 生成列配置javaScript代码
								$colArr = array();
								$i = 0;
								foreach ($colList as $k=>$v) {
									// 拓展配置
									if (is_array($v)) {
										foreach ($v as $key=>$val) {
											switch ($key) {
												case 'details':
													$detData = json_encode($val, JSON_UNESCAPED_UNICODE);
														$this->js .= <<<search
														details('$this->table_id', '$detData');
search;
												break;
												case 'table':
													$detData = json_encode($val, JSON_UNESCAPED_UNICODE);
														$this->js .= <<<search
														details_v1('$this->table_id', '$detData');
search;
												break;
												default:
													$colArr[$i][$key] = $val;
												break;
											}
										};
										$colArr[$i]['data'] = $k;
									}else{
										$colArr[$i]['data'] = $v;
									}
										
									$colArr[$i]['defaultContent'] = isset($v['defaultContent']) ? $v['defaultContent'] : 'NULL';
									$i++;
								}
								
								// 如果第一列是选择列
								if ($thList[0] == '选择') {
									array_unshift($colArr, array('data'=>'NULL', 'defaultContent'=>'', 'className'=>'select-checkbox'));;
								}
								
								$colStr = json_encode($colArr, JSON_UNESCAPED_UNICODE);
								
								$down_tool_url = self::DOWN_TOOL_URL;
								$body .= <<<endStr
										<div class="dataTable_wrapper">
											<div class="dataTables_wrapper form-inline dt-bootstrap no-footer">
												<table id="$this->table_id" class="display table table-striped table-bordered table-hover dataTable no-footer">
													<thead>
														<tr>
															$thStr
														</tr>
													</thead>
												</table>
											</div>
										</div>
														
									<script>
										var $this->table_id = $("#$this->table_id").DataTable({
											"sDom": 'lfrTtip',
											"oTableTools": {
												"sSwfPath": "$down_tool_url",
												"aButtons": [{
													"sExtends":"xls","sButtonText":"数据导出"
												}]
											},
											"order":[[0, "desc"]],
											"displayLength": 25,
											ajax : {
												'url':"$this->ajaxTableUrl",
												'type':'POST',
												'data':function(d){
													d.extra_search =[
														{
															'req':getReqJson('$this->tab_id'),
														}
													];
													
												},
											},
											"language": {
								                "lengthMenu": "每页 _MENU_ 条记录",
								                "zeroRecords": "没有找到记录",
								                "info": "第 _PAGE_ 页 ( 总共 _PAGES_ 页 )",
								                "infoEmpty": "无记录",
								                "infoFiltered": "(从 _MAX_ 条记录过滤)",
						          		    },
											columns:$colStr,
									        select: {
									            style:    'os',
									            selector: 'td:first-child'
									        },
										});
									</script>
endStr;
							break;
							// 独立表格
							case '_table':
								$thList = isset($value['thList']) ? $value['thList'] : array();
								
								// 表行
								$thStr = '';
								foreach ($thList as $value) {
									$thStr .= '<th>'.$value.'</th>';
								}
								
								
								$body .= <<<endStr
										<div class="panel panel-default">
											<div class="panel-body">
												<div class="dataTable_wrapper">
													<div class="dataTables_wrapper form-inline dt-bootstrap no-footer">
														<table class="display table table-striped table-bordered table-hover dataTable no-footer">
															<thead>
																<tr>
																	$thStr
																</tr>
															</thead>
														</table>
													</div>
												</div>
											</div>
										</div>
endStr;
							break;
							default:
								continue;
							break;
						}
		}
		
		$this->sbody .= <<<search
		<div id="$row_id" class="row">
			$body
		</div>
		<hr/>
search;
		return $this;
	}
	
	/**
	 * 输出HTML代码
	 * @return unknown
	 */
	public function done(){
		$this->html = <<<search
		<div class="tab-pane fade active in" id="$this->tab_id">
				$this->sbody
				$this->model
				
				<script>
					$this->js
				</script>
		</div>
search;
		return $this->html;
	}
	
	private function setEditTable($codeName, $thList){
		$thArr = array();
		$html = '';
		$appHtml = '';
		foreach ($thList as $key=>$value) {
			if (is_array($value)) {
				$thArr[] = isset($value[0]) ? $value[0] : 'unKnow Col';
				
				if (isset($value[1])) {
					switch ($value[1]) {
						case 'select':
							$selectArr = isset($value[2]) ? $value[2] : array();
							
							$html .= '<td><select class="js-example-basic-single js-states form-control like_search" style="width:120%;" name=""><option value="" selected disabled>请选择</option>';
							$appHtml .= '\'<td><select class="js-example-basic-single js-states form-control like_search" style="width:120%;" name=""><option value="" selected disabled>请选择</option>';
							foreach ($selectArr as $key2=>$value2) {
								$html .= '<option value="' . $key2 . '">' . $value2 . '</option>';
								$appHtml .= '<option value="' . $key2 . '">' . $value2 . '</option>';
							}
							
							$html .= '</select></td>';
							$appHtml .= '</select></td>\',';
						break;
						default:
							$html .= '<td><input name="" class="form-control" style="width:100%;" type="text"></td>';
							$appHtml .= '\'<td><input name="" class="form-control" style="width:100%;" type="text"></td>\',';
						break;
					};
				}
			}else{
				$thArr[] = $value;
				$html .= '<td><input class="form-control" style="width:100%;" type="text"></td>';
				$appHtml .= '\'<td><input class="form-control" style="width:100%;" type="text"></td>\',';
			}
		}
		
		$thStr = $this->getThStr($thArr);
		
		$this->$codeName .= <<<search
		<div class="form-group">
			<table id="$this->add_model_edit_table_id">
	        	<thead>
		            <tr>
		                $thStr
		            </tr>
		        </thead>
		        <tbody>
					 <tr>
		                $html
            		</tr>
				</tbody>
   			</table>
		    <br/>
            <button class="btn btn-info"   onclick="addRow(this)" >添加一行</button>
		    <button class="btn btn-danger" onclick="del()">删除选中行</button>
		</div>
		<script>
			$(document).ready(function(){
			    table = $('#$this->add_model_edit_table_id').DataTable({
					"paging":   false,
			        "ordering": false,
			        "info":     false,
		            "searching" : false,
				});
			 
			    $('#$this->add_model_edit_table_id tbody').on( 'click', 'tr', function () {
			        if ( $(this).hasClass('selected') ) {
			            $(this).removeClass('selected');
			        }
			        else {
			            table.$('tr.selected').removeClass('selected');
			            $(this).addClass('selected');
			        }
			    } );
			
			    $(".like_search").select2();
			});
			            		
			function addRow(){
		    	table.row.add( [
		            $appHtml
		        ] ).draw();
		            		
            	$(".like_search").select2();
			}
			
			function del(){
			    table.row('.selected').remove().draw( false );
			}
		
		</script>
search;
		
	}
	
	/**
	 * 获取字符串格式的表头
	 * @param unknown $thList
	 * @return string
	 */
	private function getThStr($thList){
		$thStr = '';
		foreach ($thList as $value) {
			$thStr .= '<th>'.$value.'</th>';
		}
		
		return $thStr;
	}
	
	
	public function setTab(&$data, $tabId){
		// 节点ID
		$data['modId'] = 'tab_' . $tabId;
		// 数据搜索行ID
		$data['searchRowId'] = $data['modId'] . '_search_row';
		// 增删改查操作行
		$data['curdRowId'] = $data['modId'] . '_curd_row';
		
		// 数据表格ID
		$data['tableId'] = $data['modId'] . '_table';
		
		// 新增数据模版ID
		$data['addModId'] = $data['modId'] . '_add_model';
		$data['editModId'] = $data['modId'] . '_edit_model';
		
		$data['ajaxRoot'] = base_url().'index.php/' . $this->file . '/' . $this->controller . '/';
		
		// 第三方excel插件下载目录
		$data['down_tool_url'] = self::DOWN_TOOL_URL;
	}
}