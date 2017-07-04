          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Редактирование пользователя</h3>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Все поля обязательны для заполнения</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
				    {message}
                    <br />
                    <form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left" method="post">
					  <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12">Логин<span class="required">*</span>
                        </label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                          <input id="login" class="form-control col-md-7 col-xs-12" required="required" type="text" value="{login}" name="login">
                        </div>
                      </div>
					  <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12">Пароль
                        </label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                          <input id="password" class="form-control col-md-7 col-xs-12" type="text" value="{password}" name="password">
                        </div>
                      </div>
					  <div class="form-group">
					    <label class="control-label col-md-5 col-sm-5 col-xs-12">Роль</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                          {role-list}
                        </div>
					  </div>
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-5">
                          <button type="submit" class="btn btn-info">Сохранить</button>
                        </div>
                      </div>

                    </form>
                  </div>
                </div>
              </div>
            </div>
		  </div>