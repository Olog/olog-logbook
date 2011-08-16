      <applet code="gov.bnl.irmis.components.applets.ComponentTypeListApplet"
	    archive="http://irmis3.nscl.msu.edu:8080/IRMISComponents/lib/IRMISComponentsApplets.jar,http://irmis3.nscl.msu.edu:8080/IRMISComponentsService/IRMISComponentsAPI.jar"
	    style="border-width:1px; border-color:blue;" width=230 height=300
	    id="applet1">
	 <param name="irmis.service"
	    value="http://irmis3.nscl.msu.edu:8080/IRMISComponentsService" />
	 <param name="java.util.logging.config.file"
	    value="http://irmis3.nscl.msu.edu:8080/IRMISComponents/logging.properties" />
	 <param name="progressbar" value="true" />
	 <param name="draggable" value="true" />
	 <param name="separate_jvm" value="true">
	 <param name="image" value="img/image--plus.png">
	 <param name="boxborder" value="false">
	 <param name="centerimage" value="true">
      </applet>
      	<form>
	<fieldset>
		<label for="name">Name</label>
		<input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" />
		<label for="email">Email</label>
		<input type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all" />
		<label for="password">Password</label>
		<input type="password" name="password" id="password" value="" class="text ui-widget-content ui-corner-all" />
	</fieldset>
	</form>