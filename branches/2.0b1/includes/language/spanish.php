<?php
//spanish
if (!isset($_SESSION['settings']['cpassman_url'])) {
$cpassman_url = '';
}else{
$cpassman_url = $_SESSION['settings']['cpassman_url'];
}

$txt['account_is_locked'] = "Esta cuenta esta bloqueada";
$txt['activity'] = "Actividad";
$txt['add_button'] = "Agregar";
$txt['add_new_group'] = "Agregar nueva carpeta";
$txt['add_role_tip'] = "Agregar nuevo rol";
$txt['admin'] = "Administracion";
$txt['admin_action'] = "Por favor valide su accion";
$txt['admin_actions_title'] = "Acciones especificas";
$txt['admin_action_check_pf'] = "Actualizar Carpetas Personales para todos los usuarios (las crea si no existen)";
$txt['admin_action_db_backup'] = "Crear una copia de seguridad de la base de datos";
$txt['admin_action_db_backup_key_tip'] = "Por favor introduzca la clave de encriptacion. Guardela en algun lado, se le pedira para una restauracion. (dejar en blanco para no encriptar)";
$txt['admin_action_db_backup_start_tip'] = "Empezar";
$txt['admin_action_db_backup_tip'] = "Es una buena practica crear una copia de seguridad que pueda ser usada para restaurar su base de datos.";
$txt['admin_action_db_clean_items'] = "Eliminar elementos huerfanos de la base de datos";
$txt['admin_action_db_clean_items_result'] = "Se han borrado elementos.";
$txt['admin_action_db_clean_items_tip'] = "Esto solo borrara los elementos y logs asociados que no han sido borrados despues de que la carpeta asociada ha sido eliminada. Se sugiere crear una copia de seguridad previamente.";
$txt['admin_action_db_optimize'] = "Optimizar la base de datos";
$txt['admin_action_db_restore'] = "Restaurar la base de datos";
$txt['admin_action_db_restore_key'] = "Por favor ingrese la clave de encriptacion.";
$txt['admin_action_db_restore_tip'] = "Se ha de hacer utilizando un archivo de copia de seguridad de SQL creado con la funcion de copia de seguridad.";
$txt['admin_action_purge_old_files'] = "Purgar archivos viejos";
$txt['admin_action_purge_old_files_result'] = "archivos han sido eliminados.";
$txt['admin_action_purge_old_files_tip'] = "Esto borrara todos los archivos temporales con mas de 7 dias.";
$txt['admin_action_reload_cache_table'] = "Recargar tabla Cache";
$txt['admin_action_reload_cache_table_tip'] = "Permite recargar todo el contenido de la tabla de Cache. Puede ser útil hacerlo a veces.";
$txt['admin_backups'] = "Copias de seguridad";
$txt['admin_error_no_complexity'] = "(<a href=\"%5C%27index.php?page=manage_groups%5C%27\">Definir?</a>)";
$txt['admin_error_no_visibility'] = "Nadie puede ver este elemento. (<a href='index.php?page=manage_roles'>Personalizar roles</a>)";
$txt['admin_functions'] = "Administracion de Roles";
$txt['admin_groups'] = "Administracion de Carpetas";
$txt['admin_help'] = "Ayuda";
$txt['admin_info'] = "Informacion referente a la herramienta";
$txt['admin_info_loading'] = "Cargando informacion... espere por favor";
$txt['admin_ldap_configuration'] = "Configuracion LDAP";
$txt['admin_ldap_menu'] = "Opciones de LDAP";
$txt['admin_main'] = "Informacion";
$txt['admin_misc_cpassman_dir'] = "Ruta completa a cPassMan";
$txt['admin_misc_cpassman_url'] = "URL completa a cPassMan";
$txt['admin_misc_custom_login_text'] = "Texto de login personalizado";
$txt['admin_misc_custom_logo'] = "Direcion URL completa del logotipo personalizado";
$txt['admin_misc_favicon'] = "Ruta completa al archivo favicon";
$txt['admin_misc_title'] = "Personalizar";
$txt['admin_one_shot_backup'] = "Un clique para copia y restauracion de seguridad";
$txt['admin_script_backups'] = "Configuracion para el Script de copia de seguridad";
$txt['admin_script_backups_tip'] = "Para mayor seguridad, se recomienda un parámetro de la copia de seguridad programada de su base de datos<br /> Use en su servidor para programar una tarea de cron diario llamando 'script.backup.php' el archivo en la carpeta 'backups'. <br /> primero tiene que establecer el 2 paramteres primero y guardarlos.";
$txt['admin_script_backup_decrypt'] = "Nombre del archivo que deseas desencriptar";
$txt['admin_script_backup_decrypt_tip'] = "Con el fin de desencriptar un archivo de copia de seguridad, sólo indicar el nombre del archivo de copia de seguridad (sin extensión y sin ruta). <br /> El archivo se desencriptara en la misma carpeta que los archivos de copia de seguridad.";
$txt['admin_script_backup_encryption'] = "Llave de criptografia (opcional)";
$txt['admin_script_backup_encryption_tip'] = "Si se establece, esta clave se utiliza para el archivo codificado";
$txt['admin_script_backup_filename'] = "Nombre del archivo de seguridad";
$txt['admin_script_backup_filename_tip'] = "Nombre de archivo que desea para el archivo de copias de seguridad";
$txt['admin_script_backup_path'] = "Ruta en la que las copias de seguridad tienen que ser almacenados";
$txt['admin_script_backup_path_tip'] = "¿En qué carpeta los archivos de copia de seguridad tienen que ser almacenados?";
$txt['admin_settings'] = "Ajustes";
$txt['admin_settings_title'] = "Ajustes de cPassMan";
$txt['admin_setting_activate_expiration'] = "Habilitar expiracion de contraseñas";
$txt['admin_setting_activate_expiration_tip'] = "Si esta activado, los elementos expirados no les seran mostrados a los usuarios";
$txt['admin_users'] = "Administracion de usuarios";
$txt['admin_views'] = "Vistas";
$txt['alert_message_done'] = "Hecho!";
$txt['alert_message_personal_sk_missing'] = "Debe ingresar su saltkey personal!";
$txt['all'] = "todo";
$txt['anyone_can_modify'] = "Permitir que este elemento sea modificado por cualquiera que pueda acceder a el";
$txt['associated_role'] = "A que rol asociar esta carpeta:";
$txt['associate_kb_to_items'] = "Seleccione los elementos asociados a esta Base de conocimientos";
$txt['assoc_authorized_groups'] = "Carpetas Asociadas Permitidas";
$txt['assoc_forbidden_groups'] = "Carpetas Asociadas Prohibidas";
$txt['at'] = "en";
$txt['at_add_file'] = "Archivo agregado";
$txt['at_category'] = "Carpeta";
$txt['at_copy'] = "Copia criada";
$txt['at_copy'] = "Copia realizada";
$txt['at_creation'] = "Creacion";
$txt['at_delete'] = "Eliminacion";
$txt['at_del_file'] = "Archivo eliminado";
$txt['at_description'] = "Descripcion.";
$txt['at_file'] = "Archivo";
$txt['at_import'] = "Importación";
$txt['at_label'] = "Etiqueta";
$txt['at_login'] = "Login";
$txt['at_modification'] = "Modificacion";
$txt['at_moved'] = "Moved";
$txt['at_personnel'] = "Personal";
$txt['at_pw'] = "Contraseña cambiada";
$txt['at_restored'] = "Restaurado";
$txt['at_shown'] = "Acceso";
$txt['at_url'] = "URL";
$txt['auteur'] = "Autor";
$txt['author'] = "Autor";
$txt['authorized_groups'] = "Carpetas permitidas";
$txt['auth_creation_without_complexity'] = "Permitir crear un elemento sin respetar la complejidad de clave requerida";
$txt['auth_modification_without_complexity'] = "Permitir modificar un elemento sin respetar la complejidad de clave requerida";
$txt['auto_create_folder_role'] = "Crear carpeta y rol para";
$txt['block_last_created'] = "Creado por ultima vez";
$txt['bugs_page'] = "Si descubre un bug, puede postearlo directamente en <a href='http://code.google.com/p/cpassman/issues/list' target='_blank'><u>Bugs</u></a>.";
$txt['by'] = "por";
$txt['cancel'] = "Cancelar";
$txt['cancel_button'] = "Cancelar";
$txt['can_create_root_folder'] = "Puede crear carpetas en el nivel raiz";
$txt['changelog'] = "Ultimas noticias";
$txt['change_authorized_groups'] = "Cambiar carpetas autorizadas";
$txt['change_forbidden_groups'] = "Cambiar carpetas prohibidas";
$txt['change_function'] = "Cambiar roles";
$txt['change_group_autgroups_info'] = "Elegir las carpetas autorizadas que este Rol puede ver y usar";
$txt['change_group_autgroups_title'] = "Personalizar las carpetas autorizadas";
$txt['change_group_forgroups_info'] = "Seleccionar las carpetas prohibidas que este Rol no puede ver ni usar";
$txt['change_group_forgroups_title'] = "Personalizar carpetas prohibidas";
$txt['change_user_autgroups_info'] = "Seleccionar las carpetas autorizadas que esta cuenta puede ver y usar";
$txt['change_user_autgroups_title'] = "Personalizar las carpetas autorizadas";
$txt['change_user_forgroups_info'] = "Seleccionar las carpetas prohibidas que esta cuenta no puede ver ni usar";
$txt['change_user_forgroups_title'] = "Personalizar carpetas prohibidas";
$txt['change_user_functions_info'] = "Seleccionar las funciones asociadas a esta cuenta";
$txt['change_user_functions_title'] = "Personalizar funciones asociadas";
$txt['check_all_text'] = "Elegir todo";
$txt['close'] = "Cerrar";
$txt['complexity'] = "Complejidad";
$txt['complex_asked'] = "Complejidad requerida";
$txt['complex_asked'] = "Complejidad requerida";
$txt['complex_level0'] = "Muy debil";
$txt['complex_level1'] = "Debil";
$txt['complex_level2'] = "Media";
$txt['complex_level3'] = "Fuerte";
$txt['complex_level4'] = "Muy fuerte";
$txt['complex_level5'] = "Pesada";
$txt['complex_level6'] = "Muy pesada";
$txt['confirm'] = "Confirmar";
$txt['confirm_delete_group'] = "Ha decidido eliminar esta Carpeta y todos los elementos incluidos en ella... Esta seguro?";
$txt['confirm_deletion'] = "Va a eliminar... esta seguro?";
$txt['confirm_del_account'] = "Ha decidido borrar esta Cuenta. Esta seguro?";
$txt['confirm_del_from_fav'] = "Por favor confirme la eliminacion de Favoritos";
$txt['confirm_del_role'] = "Por favor confirme la eliminacion del siguiente rol:";
$txt['confirm_edit_role'] = "Por favor introduzca el nombre del siguiente rol:";
$txt['confirm_lock_account'] = "Usted ha decidido bloquear esta cuenta. ¿Está seguro?";
$txt['connection'] = "Conexion";
$txt['connections'] = "Conexiones";
$txt['copy'] = "Copiar";
$txt['copy_to_clipboard_small_icons'] = "Activar los iconos de copiar al portapapeles en la página de Elementos";
$txt['copy_to_clipboard_small_icons_tip'] = "<span style='font-size:11px;max-width:300px;'>Esto puede ayudar a prevenir el consumo de memoria si los usuarios no tienen un ordenador actual.<br /> De hecho, no se cargara la información de los elementos en el portapapeles. Sin embargo, no hay una forma rápida posible de copiar la contraseña y el login.</span>";
$txt['creation_date'] = "Fecha de creacion";
$txt['csv_import_button_text'] = "Navegar archivo CSV";
$txt['date'] = "Fecha";
$txt['date'] = "Fecha";
$txt['date_format'] = "Formato de la fecha";
$txt['days'] = "Dias";
$txt['definition'] = "Definicion";
$txt['delete'] = "Eliminar";
$txt['deletion'] = "Eliminaciones ";
$txt['deletion_title'] = "Lista de elementos eliminados";
$txt['del_button'] = "Eliminar";
$txt['del_function'] = "Eliminar Roles";
$txt['del_group'] = "Eliminar Carpeta";
$txt['description'] = "Descripcion";
$txt['description'] = "Descripcion";
$txt['disconnect'] = "Desconexion";
$txt['disconnection'] = "Desconexion";
$txt['div_dialog_message_title'] = "Informacion";
$txt['done'] = "Hecho";
$txt['drag_drop_helper'] = "Arrastre y suelte el iten";
$txt['duplicate_folder'] = "Permitir varias carpetas con el mismo nombre.";
$txt['duplicate_item'] = "Permitir varios elementos con el mismo nombre.";
$txt['email'] = "Email";
$txt['email_altbody_1'] = "Elemento";
$txt['email_altbody_2'] = "ha sido creado";
$txt['email_announce'] = "Anunciar este elemento por email";
$txt['email_body1'] = "Hola,<br><br>Elemento '";
$txt['email_body2'] = "ha sido creado.<br /><br />Puede verlo haciendo click en <a href='";
$txt['email_body3'] = "'>AQUI</a><br /><br />Saludos.";
$txt['email_change'] = "Cambie el email de la cuenta";
$txt['email_changed'] = "Email cambiado!";
$txt['email_select'] = "Seleccionar personas a informar";
$txt['email_subject'] = "Creando un nuevo elemento en el Administrador de Contraseñas";
$txt['email_subject_new_user'] = "[cPassMan] Creación de su cuenta";
$txt['email_text_new_user'] = "Hola,<br /><br />Su cuenta ha sido creada en cPassMan.<br />Puede acceder a $cpassman_url utilizando las siguientes credenciales:<br />";
$txt['enable_favourites'] = "Permitir al Usuario almacenar Favoritos";
$txt['enable_personal_folder'] = "Habilitar carpeta Personal";
$txt['enable_personal_folder_feature'] = "Habilitar la opcion de carpeta Personal";
$txt['enable_user_can_create_folders'] = "Los usuarios pueden administrar las carpetas y en las carpetas autorizadas";
$txt['encrypt_key'] = "Clave de encriptacion";
$txt['errors'] = "errores";
$txt['error_complex_not_enought'] = "No se ha alcanzado la complejidad de la contraseña!";
$txt['error_confirm'] = "La confirmacion de la contraseña no es correcta!";
$txt['error_cpassman_dir'] = "No hay una ruta establecida para cPassMan. Por favor, seleccione la pestaña 'Configuracion de cPassMan' en la pagina de Configuraciones de Administrador.";
$txt['error_cpassman_url'] = "No se ha definido la URL para cPassMan. Seleccione la pestaña 'Configuración de cPassMan' en la página de configuración.";
$txt['error_fields_2'] = "Los 2 campos son obligatorios!";
$txt['error_group'] = "Una carpeta es obligatoria!";
$txt['error_group_complex'] = "La Carpeta debe teber un nivel de complejidad de contraseña minimo requerido!";
$txt['error_group_exist'] = "La carpeta ya existe!";
$txt['error_group_label'] = "La Carpeta debe estar nombrada!";
$txt['error_html_codes'] = "Algun texto contiene codigo HTML! Esto no esta permitido.";
$txt['error_item_exists'] = "El elemento ya existe!";
$txt['error_label'] = "Una etiqueta es obligatoria!";
$txt['error_must_enter_all_fields'] = "¡Tiene que completar cada campo!";
$txt['error_mysql'] = "Error de MySQL!";
$txt['error_not_authorized'] = "No esta autorizado a ver esta pagina.";
$txt['error_not_exists'] = "Esta pagina no existe.";
$txt['error_no_folders'] = "Deberia empezar creando alguna carpeta.";
$txt['error_no_password'] = "Debe ingresar su contraseña!";
$txt['error_no_roles'] = "Deberia crear algunos roles y asociarlos a carpetas.";
$txt['error_password_confirmation'] = "La contraseña deberia ser la misma";
$txt['error_pw'] = "Una contraseña es obligatoria!";
$txt['error_renawal_period_not_integer'] = "El periodo de renovacion debe estar expresado en meses!";
$txt['error_salt'] = "<b>¡La SALT Key es demasiado larga! No utilice la herramienta hasta que un Administrador modifique su Salt Key.</b> En el archivo settings.php, SALT no puede ser superior a 32 caracteres.";
$txt['error_tags'] = "No se permite caracteres de puntuacion en las ETIQUETAS! Solo espacios.";
$txt['error_user_exists'] = "El usuario ya existe";
$txt['expiration_date'] = "Fecha de expiracion";
$txt['expir_one_month'] = "1 mes";
$txt['expir_one_year'] = "1 año";
$txt['expir_six_months'] = "6 meses";
$txt['expir_today'] = "hoy";
$txt['files_&_images'] = "Archivos e Imagenes";
$txt['find'] = "Buscar";
$txt['find_text'] = "Su busqueda";
$txt['folders'] = "Carpetas";
$txt['forbidden_groups'] = "Carpetas prohibidas";
$txt['forgot_my_pw'] = "Olvido su contraseña?";
$txt['forgot_my_pw_email_sent'] = "El email ha sido enviado";
$txt['forgot_my_pw_error_email_not_exist'] = "Este email no existe!";
$txt['forgot_my_pw_text'] = "Su contraseña le sera enviada al email asociado a su cuenta";
$txt['forgot_pw_email_altbody_1'] = "Hola, sus credenciales para cPassMan son:";
$txt['forgot_pw_email_body'] = "Hola,<br><br>Su nueva contraseña para acceder a cPassMan es:";
$txt['forgot_pw_email_body'] = "Hola,<br /><br />Su nueva contraseña para cPassMan es:";
$txt['forgot_pw_email_body_1'] = "Hola, <br /><br />sus credenciales para cPassMan son:<br /><br />";
$txt['forgot_pw_email_subject'] = "cPassMan - Su contraseña";
$txt['forgot_pw_email_subject_confirm'] = "cPassMan - Su contraseña paso 2";
$txt['functions'] = "Roles";
$txt['function_alarm_no_group'] = "Este rol no esta asociado a ninguna Carpeta!";
$txt['generate_pdf'] = "Generar un archivo PDF";
$txt['generation_options'] = "Opciones de generacion";
$txt['gestionnaire'] = "Manager";
$txt['give_function_tip'] = "Agregar un nuevo rol";
$txt['give_function_title'] = "agregar un nuevo Rol";
$txt['give_new_email'] = "Por favor introduzca un nuevo email para";
$txt['give_new_login'] = "Por favor seleccione la cuenta";
$txt['give_new_pw'] = "Por favor indique la nueva contraseña para";
$txt['god'] = "DIOS";
$txt['group'] = "Carpeta";
$txt['group_parent'] = "Carpeta Padre";
$txt['group_pw_duration'] = "Periodo de renovacion";
$txt['group_pw_duration_tip'] = "En meses. Use 0 para deshabilitar";
$txt['group_select'] = "Seleccionar carpeta";
$txt['group_title'] = "Etiqueta de la carpeta";
$txt['history'] = "Historial";
$txt['home'] = "Home";
$txt['home_personal_menu'] = "Acciones Personales";
$txt['home_personal_saltkey'] = "Su SALTKey personal";
$txt['home_personal_saltkey_button'] = "Guardar!";
$txt['home_personal_saltkey_info'] = "Deberia ingresar su saltkey personal si necesita usar sus elementos personales.";
$txt['home_personal_saltkey_label'] = "Ingrese su saltkey personal";
$txt['importing_details'] = "Lista de detalles";
$txt['importing_folders'] = "Importando carpetas";
$txt['importing_items'] = "Importando elementos";
$txt['import_button'] = "Importar";
$txt['import_csv_anyone_can_modify_in_role_txt'] = "Activar \"todos los del mismo rol pueden modificar\" en todos los elementos importados.";
$txt['import_csv_anyone_can_modify_txt'] = "Activar \"todos pueden modificar\" en todos los elementos importados.";
$txt['import_csv_dialog_info'] = "Informacion: La importacion debe ser hecha usando un archivo CSV. Generalmente un archivo exportado desde KeePass tiene la estructura esperada.<br />Si usted usa un archivo generado por otra herramienta, por favor chequee que la estructura CSV sea la siguiente: 'Cuenta','Nombre de usuario','Clave','Sitio web','Comentarios'.";
$txt['import_csv_menu_title'] = "Importar elementos desde archivo (CSV/KeePass XML)";
$txt['import_error_no_file'] = "Debe seleccionar un archivo!";
$txt['import_error_no_read_possible'] = "No se puede leer el archivo!";
$txt['import_error_no_read_possible_kp'] = "No se puede leer el archivo! Debe ser un archivo KeePass.";
$txt['import_keepass_dialog_info'] = "Por favor use esto para seleccionar un archivo XML generado por la funcionalidad de exportacion de KeePass. Solo va a funcionar con un archivo KeePass! Tenga en cuenta que el script de importacion no importara carpetas o elementos que ya existan en el mismo nivel de la estructura de arbol.";
$txt['import_keepass_to_folder'] = "Seleccione la carpeta de destino";
$txt['import_kp_finished'] = "La Importacion desde KeePass ha finalizado!<br>Por defecto, el nivel de complejidad para las nuevas carpetas ha sido establecido a 'Medio'. Quizas necesite cambiarlo.";
$txt['import_to_folder'] = "Seleccione los elementos que quiere importar a la carpeta:";
$txt['index_add_one_hour'] = "Extender la sesion 1 hora";
$txt['index_alarm'] = "ALARMA!!!";
$txt['index_bas_pw'] = "Contraseña erronea para esta cuenta!";
$txt['index_change_pw'] = "Debe cambiar su contraseña!";
$txt['index_change_pw'] = "Cambie su contraseña";
$txt['index_change_pw_button'] = "Cambiar";
$txt['index_change_pw_confirmation'] = "Confirmar";
$txt['index_expiration_in'] = "la sesion expira en";
$txt['index_get_identified'] = "Por favor identifiquese";
$txt['index_identify_button'] = "Entrar";
$txt['index_identify_you'] = "Por favor identifiquese";
$txt['index_last_pw_change'] = "Contraseña cambiada el";
$txt['index_last_seen'] = "Ultima conexion, el";
$txt['index_login'] = "Cuenta";
$txt['index_maintenance_mode'] = "Modo de mantenimiento activado. Solo los Administradores pueden ingresar.";
$txt['index_maintenance_mode_admin'] = "Modo de mantenimiento activado. En este momento los usuarios no pueden acceder a cPassMan.";
$txt['index_new_pw'] = "Nueva contraseña";
$txt['index_password'] = "Contraseña";
$txt['index_pw_error_identical'] = "Las contraseñas deben ser identicas!";
$txt['index_pw_expiration'] = "Expiracion de la contraseña actual en";
$txt['index_pw_level_txt'] = "Complejidad";
$txt['index_refresh_page'] = "Actualizar pagina";
$txt['index_session_duration'] = "Duracion de la sesion";
$txt['index_session_ending'] = "Su sesion terminara en menos de 1 minuto.";
$txt['index_session_expired'] = "Su sesion ha expirado o no esta correctamente identificado!";
$txt['index_welcome'] = "Bienvenido";
$txt['info'] = "Informacion";
$txt['info_click_to_edit'] = "Pulse en una celda para editar su valor";
$txt['is_admin'] = "Es Admin";
$txt['is_manager'] = "Es Manager";
$txt['is_read_only'] = "Is Read Only";
$txt['items_browser_title'] = "Carpetas";
$txt['item_copy_to_folder'] = "Por favor, seleccione una carpeta en la que el tema tiene que ser copiado.";
$txt['item_menu_add_elem'] = "Agregar elemento";
$txt['item_menu_add_rep'] = "Agregar una Carpeta";
$txt['item_menu_add_to_fav'] = "Agregar a Favoritos";
$txt['item_menu_collab_disable'] = "La edicion no esta permitida";
$txt['item_menu_collab_enable'] = "La edicion esta permitida";
$txt['item_menu_copy_elem'] = "Copiar elemento";
$txt['item_menu_copy_login'] = "Copiar login";
$txt['item_menu_copy_pw'] = "Copiar contraseña";
$txt['item_menu_del_elem'] = "Eliminar elemento";
$txt['item_menu_del_from_fav'] = "Eliminar de Favoritos";
$txt['item_menu_del_rep'] = "Eliminar una Carpeta";
$txt['item_menu_edi_elem'] = "Editar elemento";
$txt['item_menu_edi_rep'] = "Editar una Carpeta";
$txt['item_menu_find'] = "Buscar";
$txt['item_menu_mask_pw'] = "Enmascarar contraseña";
$txt['item_menu_refresh'] = "Actualizar pagina";
$txt['kbs'] = "KBs";
$txt['kb_menu'] = "Base de conocimientos";
$txt['keepass_import_button_text'] = "Navegar archivo XML";
$txt['label'] = "Etiqueta";
$txt['last_items_icon_title'] = "Mostrar/Ocultar el ultimo elemento visto";
$txt['last_items_title'] = "Ultimo elemento visto";
$txt['ldap_extension_not_loaded'] = "La extensión LDAP no está activado en el servidor.";
$txt['level'] = "Nivel";
$txt['link_copy'] = "Obtener un link a este item";
$txt['link_is_copied'] = "El link a este elemento ha sido copiado al clipboard.";
$txt['login'] = "Login (si es necesario)";
$txt['login_attempts_on'] = "Intentos de ingreso en";
$txt['login_copied_clipboard'] = "Login copiado al portapapeles";
$txt['login_copy'] = "Copiar cuenta al portapapeles";
$txt['logs'] = "Logs";
$txt['logs_1'] = "Generar el archivo log para las contraseñas cambiadas el";
$txt['logs_passwords'] = "Generar log de Contraseñas";
$txt['maj'] = "Letras mayusculas";
$txt['mask_pw'] = "Enmascarar/Mostrar la contraseña";
$txt['max_last_items'] = "Maximo numero de ultimos elementos vistos por el usuario (por defecto es 10)";
$txt['minutes'] = "minutos";
$txt['modify_button'] = "Modificar";
$txt['my_favourites'] = "Mis favoritos";
$txt['name'] = "Nombre";
$txt['nb_false_login_attempts'] = "Numero de intentos de autenticacion incorrectos antes de deshabilitar la cuenta (0 para deshabilitar)";
$txt['nb_folders'] = "Numero de Carpetas";
$txt['nb_items'] = "Numero de elementos";
$txt['nb_items_by_page'] = "Número de artículos por página";
$txt['new_label'] = "Nueva etiqueta";
$txt['new_role_title'] = "Nuevo titulo de rol";
$txt['new_user_title'] = "Agregar un nuevo usuario";
$txt['no'] = "No";
$txt['nom'] = "Nombre";
$txt['none'] = "Ninguno";
$txt['none_selected_text'] = "Ningun elemento seleccionado";
$txt['not_allowed_to_see_pw'] = "No esta autorizado a ver ese elemento!";
$txt['not_allowed_to_see_pw_is_expired'] = "Este elemento ha expirado!";
$txt['not_defined'] = "No definido";
$txt['no_last_items'] = "No se ven elementos";
$txt['no_restriction'] = "Sin restriccion";
$txt['numbers'] = "Numeros";
$txt['number_of_used_pw'] = "Numero de contraseñas nuevas que el usuario debe ingresar ante de reusar una contraseña vieja.";
$txt['ok'] = "OK";
$txt['pages'] = "Paginas";
$txt['pdf_del_date'] = "El PDF ha generado el";
$txt['pdf_del_title'] = "Seguimiento de la renovacion de contraseñas";
$txt['pdf_download'] = "Descargar archivo";
$txt['personal_folder'] = "Carpeta personal";
$txt['personal_salt_key'] = "Su salt key personal";
$txt['personal_salt_key_empty'] = "Su salt key personal no se ha introducido!";
$txt['personal_salt_key_info'] = "Esta salt key sera usada para encriptar y desencriptar sus contraseñas.<br />No se almacena en la base de datos, usted es la unica persona que la sabe.<br />Por lo tanto, no la pierda!";
$txt['please_update'] = "Por favor actualice la herramienta!";
$txt['print'] = "Imprimir";
$txt['print_out_menu_title'] = "Imprimir listado de sus elementos";
$txt['print_out_pdf_title'] = "cPassMan - Lista de elementos exportados";
$txt['print_out_warning'] = "Todos las claves y datos confidenciales seran escritos en este archivo sin ninguna enciptacion! Al escribir el archivo que contiene items/claves no encriptados, usted esta asumiendo la completa responsabilidad de la proteccion de esta lista!";
$txt['pw'] = "Contraseña";
$txt['pw_change'] = "Cambiar la contraseña de la cuenta";
$txt['pw_changed'] = "Contraseña cambiada!";
$txt['pw_copied_clipboard'] = "Contraseña copiada al portapapeles";
$txt['pw_copy_clipboard'] = "Copiar contraseña al portapapeles";
$txt['pw_generate'] = "Generar";
$txt['pw_is_expired_-_update_it'] = "El elemento ha expirado! Debe cambiar su contraseña.";
$txt['pw_life_duration'] = "Tiempo de vida de la contraseña de los usuarios antes de expirar (en dias, 0 para deshabilitar)";
$txt['pw_recovery_asked'] = "Usted ha solicitado una recuperación de contraseña";
$txt['pw_recovery_button'] = "Enviar mi nueva contraseña";
$txt['pw_recovery_info'] = "Pulsando el siguiente botón, va a recibir un eMail que va a contener la nueva contraseña para su cuenta.";
$txt['pw_used'] = "Esta contraseña ya se ha usado!";
$txt['readme_open'] = "Abrir archivo README completo";
$txt['read_only_account'] = "Read Only";
$txt['refresh_matrix'] = "Actualizar Matriz";
$txt['renewal_menu'] = "Seguimiento de la renovacion";
$txt['renewal_needed_pdf_title'] = "Lista de elementos que deben ser renovados";
$txt['renewal_selection_text'] = "Listar todos los elementos que van a expirar:";
$txt['restore'] = "Restaurar";
$txt['restore'] = "Restaurar";
$txt['restricted_to'] = "Restringido a";
$txt['restricted_to_roles'] = "Permitir restringir Elementos a Usuarios y Roles";
$txt['rights_matrix'] = "Matriz de permisos de usuario";
$txt['roles'] = "Roles";
$txt['role_cannot_modify_all_seen_items'] = "Activar el rol sin permiso de modificación a todos los elementos accesibles (opción habitual)";
$txt['role_can_modify_all_seen_items'] = "Activar el rol con permisos de modificación en todos los elementos accesibles (opción no segura)";
$txt['root'] = "Raiz";
$txt['save_button'] = "Salvar";
$txt['secure'] = "Seguro";
$txt['see_logs'] = "Ver los registros de logs";
$txt['select'] = "elegir";
$txt['select_folders'] = "Seleccionar carpetas";
$txt['select_language'] = "Selecciones su idioma";
$txt['send'] = "Enviar";
$txt['settings_anyone_can_modify'] = "Activar una opcion para cada elemento que le permita a cualquier persona modificarlo";
$txt['settings_anyone_can_modify_tip'] = "<span style='font-size:11px;max-width:300px;'>Cuando está activado, esto va a añadir una casilla en el Elemento que permitirá al creador la capacidad de permitir la modificación de este Elemento a cualquier usuario.</span>";
$txt['settings_kb'] = "Habilitar Base de Conocimientos (beta)";
$txt['settings_kb_tip'] = "<span style='font-size:11px;max-width:300px;'>Una vez activado, esto agregara una pagina en la cual usted puede construir su base de conocimientos.</span>";
$txt['settings_ldap_domain'] = "Sufijo de cuentas LDAP para su dominio";
$txt['settings_ldap_domain_controler'] = "Array de controladores de dominio LDAP";
$txt['settings_ldap_domain_controler_tip'] = "<span style=\"font-size:11px;max-width:300px;\">Especifique multiples controladores si desea que la clase balancee las consultas LDAP entre los multiples servidores.<br>Debe delimitar los dominios con una coma ( , )!<br>Por ejemplo: dominio_1,dominio_2,dominio_3</span>";
$txt['settings_ldap_domain_dn'] = "Base DN LDAP para su dominio";
$txt['settings_ldap_mode'] = "Permitir autenticacion de usuarios a traves de servidor LDAP";
$txt['settings_ldap_mode_tip'] = "Habilitar solamente si usted tiene un servidor LDAP y desea utilizarlo para autenticar los usuarios de cPassMan.";
$txt['settings_ldap_ssl'] = "Usar LDAP a traves de SSL (LDAPS)";
$txt['settings_ldap_tls'] = "Usar LDAP a traves de TSL";
$txt['settings_log_accessed'] = "Habilitar registros de quien accedieron a los elementos";
$txt['settings_log_connections'] = "Habilitar el logging de todas las conexiones de los usuarios a la base de datos.";
$txt['settings_maintenance_mode'] = "Poner cPassMan en Modo Mantenimiento";
$txt['settings_maintenance_mode_tip'] = "Este modo negara el acceso a cualquier usuario con excepcion de los Administradores.";
$txt['settings_manager_edit'] = "Los Managers puede editar y eliminar los elementos que pueden ver";
$txt['settings_printing'] = "Habilitar la impresion de elementos a archivos PDF";
$txt['settings_printing_tip'] = "Una vez habilitado, aparecera un boton en la pagina inicial del usuario que le permitira escribir un listado de elementos en un archivo PDF que podra ver. Tenga en cuenta que el listado de claves sera desencriptado.";
$txt['settings_richtext'] = "Habilitar texto enriquecido para la descripcion de elementos";
$txt['settings_richtext_tip'] = "<span style='font-size:11px;max-width:300px;'>Esto activa la edición de texto enriquecido con BBCodes en el campo de descripción.</span>";
$txt['settings_send_stats'] = "Enviar estadisticas mensuales al autor para un mejor entendimiento del uso de cPassMan";
$txt['settings_send_stats_tip'] = "Estas estadisticas son completamente anonimas!<br /><span style='font-size:10px;max-width:300px;'>Su IP no sera enviada, solamente la siguiente informacion sera transmitida: cantidad de Items, Carpetas, Usuarios, version de cPassman, carpetas personales habilitadas, ldap habilitado.<br />Le agradecemos por adelantado si habilita dichas estadisticas. Con esto me ayuda a seguir desarrollando cPassMan.</span>";
$txt['settings_show_description'] = "Show Description in list of Items";
$txt['show'] = "Mostrar";
$txt['show_help'] = "Mostrar Ayuda";
$txt['show_last_items'] = "Mostrar el bloque de los últimos elementos en la página principal";
$txt['size'] = "Tamaño";
$txt['start_upload'] = "Empezar a subir archivos";
$txt['sub_group_of'] = "Depende de ";
$txt['support_page'] = "Para cualquier tipo de soporte por favor use el <a href='https://sourceforge.net/projects/communitypasswo/forums' target='_blank'><u>Forum</u></a>.";
$txt['symbols'] = "Simbolos";
$txt['tags'] = "Etiquetas ";
$txt['thku'] = "Gracias por usar cPassMan!";
$txt['timezone_selection'] = "Zona horaria selecionada";
$txt['time_format'] = "Formato de la hora";
$txt['uncheck_all_text'] = "Deseleccionar todo";
$txt['unlock_user'] = "El usuario esta bloqueado. Desea desbloquear esta cuenta?";
$txt['update_needed_mode_admin'] = "Se recomienda actualizar su instalacion de cPassMan. Pulse <a href=\"%5C%27install/upgrade.php%5C%27\">AQUI</a>";
$txt['uploaded_files'] = "Archivos Existentes";
$txt['upload_button_text'] = "Navegar";
$txt['upload_files'] = "Cargar Archivos Nuevos";
$txt['url'] = "URL";
$txt['url_copied'] = "La URL ha sido copiada!";
$txt['used_pw'] = "Constraseña usada";
$txt['user'] = "Usuario";
$txt['users'] = "Usuarios";
$txt['user_action'] = "La acción de un usuario";
$txt['user_alarm_no_function'] = "Este usuario no tiene Roles!";
$txt['user_del'] = "Eliminar cuenta";
$txt['user_lock'] = "Bloquear usuario";
$txt['version'] = "Version actual";
$txt['views_confirm_items_deletion'] = "Desea eliminar los elementos seleccionados de la base de datos?";
$txt['views_confirm_restoration'] = "Por favor confirme la restauracion de este elemento";
$txt['visibility'] = "Visibilidad";
$txt['yes'] = "Si";
$txt['your_version'] = "Su version";
?>