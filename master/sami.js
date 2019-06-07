
window.projectVersion = 'master';

(function(root) {

    var bhIndex = null;
    var rootPath = '';
    var treeHtml = '        <ul>                <li data-name="namespace:Plasma" class="opened">                    <div style="padding-left:0px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Plasma.html">Plasma</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:Plasma_Schemas" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Plasma/Schemas.html">Schemas</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:Plasma_Schemas_Repository" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Plasma/Schemas/Repository.html">Repository</a>                    </div>                </li>                            <li data-name="class:Plasma_Schemas_RepositoryInterface" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Plasma/Schemas/RepositoryInterface.html">RepositoryInterface</a>                    </div>                </li>                            <li data-name="class:Plasma_Schemas_SQLSchemaBuilder" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Plasma/Schemas/SQLSchemaBuilder.html">SQLSchemaBuilder</a>                    </div>                </li>                            <li data-name="class:Plasma_Schemas_Schema" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Plasma/Schemas/Schema.html">Schema</a>                    </div>                </li>                            <li data-name="class:Plasma_Schemas_SchemaBuilderInterface" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Plasma/Schemas/SchemaBuilderInterface.html">SchemaBuilderInterface</a>                    </div>                </li>                            <li data-name="class:Plasma_Schemas_SchemaCollection" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Plasma/Schemas/SchemaCollection.html">SchemaCollection</a>                    </div>                </li>                            <li data-name="class:Plasma_Schemas_SchemaInterface" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Plasma/Schemas/SchemaInterface.html">SchemaInterface</a>                    </div>                </li>                            <li data-name="class:Plasma_Schemas_Statement" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Plasma/Schemas/Statement.html">Statement</a>                    </div>                </li>                            <li data-name="class:Plasma_Schemas_Transaction" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Plasma/Schemas/Transaction.html">Transaction</a>                    </div>                </li>                </ul></div>                </li>                </ul></div>                </li>                </ul>';

    var searchTypeClasses = {
        'Namespace': 'label-default',
        'Class': 'label-info',
        'Interface': 'label-primary',
        'Trait': 'label-success',
        'Method': 'label-danger',
        '_': 'label-warning'
    };

    var searchIndex = [
                    
            {"type": "Namespace", "link": "Plasma.html", "name": "Plasma", "doc": "Namespace Plasma"},{"type": "Namespace", "link": "Plasma/Schemas.html", "name": "Plasma\\Schemas", "doc": "Namespace Plasma\\Schemas"},
            {"type": "Interface", "fromName": "Plasma\\Schemas", "fromLink": "Plasma/Schemas.html", "link": "Plasma/Schemas/RepositoryInterface.html", "name": "Plasma\\Schemas\\RepositoryInterface", "doc": "&quot;The Repository Interface describes the public API of repositories.&quot;"},
                                                        {"type": "Method", "fromName": "Plasma\\Schemas\\RepositoryInterface", "fromLink": "Plasma/Schemas/RepositoryInterface.html", "link": "Plasma/Schemas/RepositoryInterface.html#method_getClient", "name": "Plasma\\Schemas\\RepositoryInterface::getClient", "doc": "&quot;Get the internally used client.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\RepositoryInterface", "fromLink": "Plasma/Schemas/RepositoryInterface.html", "link": "Plasma/Schemas/RepositoryInterface.html#method_getSchemaBuilder", "name": "Plasma\\Schemas\\RepositoryInterface::getSchemaBuilder", "doc": "&quot;Get the Schema Builder for the schema.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\RepositoryInterface", "fromLink": "Plasma/Schemas/RepositoryInterface.html", "link": "Plasma/Schemas/RepositoryInterface.html#method_registerSchemaBuilder", "name": "Plasma\\Schemas\\RepositoryInterface::registerSchemaBuilder", "doc": "&quot;Register a Schema Builder for the schema to be used by the Repository.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\RepositoryInterface", "fromLink": "Plasma/Schemas/RepositoryInterface.html", "link": "Plasma/Schemas/RepositoryInterface.html#method_unregisterSchemaBuilder", "name": "Plasma\\Schemas\\RepositoryInterface::unregisterSchemaBuilder", "doc": "&quot;Unregister the Schema Builder of the schema.&quot;"},
            
            {"type": "Interface", "fromName": "Plasma\\Schemas", "fromLink": "Plasma/Schemas.html", "link": "Plasma/Schemas/SchemaBuilderInterface.html", "name": "Plasma\\Schemas\\SchemaBuilderInterface", "doc": "&quot;Schema Builders are responsible for creating individual schemas from query results.&quot;"},
                                                        {"type": "Method", "fromName": "Plasma\\Schemas\\SchemaBuilderInterface", "fromLink": "Plasma/Schemas/SchemaBuilderInterface.html", "link": "Plasma/Schemas/SchemaBuilderInterface.html#method_setRepository", "name": "Plasma\\Schemas\\SchemaBuilderInterface::setRepository", "doc": "&quot;Sets the repository to use.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SchemaBuilderInterface", "fromLink": "Plasma/Schemas/SchemaBuilderInterface.html", "link": "Plasma/Schemas/SchemaBuilderInterface.html#method_fetch", "name": "Plasma\\Schemas\\SchemaBuilderInterface::fetch", "doc": "&quot;Fetch a row by the unique identifier. Resolves with an instance of &lt;code&gt;SchemaCollection&lt;\/code&gt;.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SchemaBuilderInterface", "fromLink": "Plasma/Schemas/SchemaBuilderInterface.html", "link": "Plasma/Schemas/SchemaBuilderInterface.html#method_fetchBy", "name": "Plasma\\Schemas\\SchemaBuilderInterface::fetchBy", "doc": "&quot;Fetch a row by the specified column. Resolves with an instance of &lt;code&gt;SchemaCollection&lt;\/code&gt;.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SchemaBuilderInterface", "fromLink": "Plasma/Schemas/SchemaBuilderInterface.html", "link": "Plasma/Schemas/SchemaBuilderInterface.html#method_fetchAll", "name": "Plasma\\Schemas\\SchemaBuilderInterface::fetchAll", "doc": "&quot;Fetches all rows. Resolves with an instance of &lt;code&gt;SchemaCollection&lt;\/code&gt;.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SchemaBuilderInterface", "fromLink": "Plasma/Schemas/SchemaBuilderInterface.html", "link": "Plasma/Schemas/SchemaBuilderInterface.html#method_insert", "name": "Plasma\\Schemas\\SchemaBuilderInterface::insert", "doc": "&quot;Inserts a row. Resolves with an instance of &lt;code&gt;SchemaCollection&lt;\/code&gt;.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SchemaBuilderInterface", "fromLink": "Plasma/Schemas/SchemaBuilderInterface.html", "link": "Plasma/Schemas/SchemaBuilderInterface.html#method_insertAll", "name": "Plasma\\Schemas\\SchemaBuilderInterface::insertAll", "doc": "&quot;Inserts a list of rows. Resolves with an instance of &lt;code&gt;SchemaCollection&lt;\/code&gt;.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SchemaBuilderInterface", "fromLink": "Plasma/Schemas/SchemaBuilderInterface.html", "link": "Plasma/Schemas/SchemaBuilderInterface.html#method_update", "name": "Plasma\\Schemas\\SchemaBuilderInterface::update", "doc": "&quot;Updates the row with the given data, identified by a specific field.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SchemaBuilderInterface", "fromLink": "Plasma/Schemas/SchemaBuilderInterface.html", "link": "Plasma/Schemas/SchemaBuilderInterface.html#method_buildSchemas", "name": "Plasma\\Schemas\\SchemaBuilderInterface::buildSchemas", "doc": "&quot;Builds schemas for the given SELECT query result.&quot;"},
            
            {"type": "Interface", "fromName": "Plasma\\Schemas", "fromLink": "Plasma/Schemas.html", "link": "Plasma/Schemas/SchemaInterface.html", "name": "Plasma\\Schemas\\SchemaInterface", "doc": "&quot;Schemas represent data rows and as such can be used to interact with the DBMS through the Repository and Schema Builder.&quot;"},
                                                        {"type": "Method", "fromName": "Plasma\\Schemas\\SchemaInterface", "fromLink": "Plasma/Schemas/SchemaInterface.html", "link": "Plasma/Schemas/SchemaInterface.html#method_build", "name": "Plasma\\Schemas\\SchemaInterface::build", "doc": "&quot;Builds a schema instance.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SchemaInterface", "fromLink": "Plasma/Schemas/SchemaInterface.html", "link": "Plasma/Schemas/SchemaInterface.html#method_getDefinition", "name": "Plasma\\Schemas\\SchemaInterface::getDefinition", "doc": "&quot;Returns the schema definition.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SchemaInterface", "fromLink": "Plasma/Schemas/SchemaInterface.html", "link": "Plasma/Schemas/SchemaInterface.html#method_getTableName", "name": "Plasma\\Schemas\\SchemaInterface::getTableName", "doc": "&quot;Returns the name of the table.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SchemaInterface", "fromLink": "Plasma/Schemas/SchemaInterface.html", "link": "Plasma/Schemas/SchemaInterface.html#method_getIdentifierColumn", "name": "Plasma\\Schemas\\SchemaInterface::getIdentifierColumn", "doc": "&quot;Returns the name of the identifier column (primary or unique), or null.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SchemaInterface", "fromLink": "Plasma/Schemas/SchemaInterface.html", "link": "Plasma/Schemas/SchemaInterface.html#method_insert", "name": "Plasma\\Schemas\\SchemaInterface::insert", "doc": "&quot;Inserts the row.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SchemaInterface", "fromLink": "Plasma/Schemas/SchemaInterface.html", "link": "Plasma/Schemas/SchemaInterface.html#method_update", "name": "Plasma\\Schemas\\SchemaInterface::update", "doc": "&quot;Updates the row with the new data.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SchemaInterface", "fromLink": "Plasma/Schemas/SchemaInterface.html", "link": "Plasma/Schemas/SchemaInterface.html#method_delete", "name": "Plasma\\Schemas\\SchemaInterface::delete", "doc": "&quot;Deletes the row.&quot;"},
            
            
            {"type": "Class", "fromName": "Plasma\\Schemas", "fromLink": "Plasma/Schemas.html", "link": "Plasma/Schemas/Repository.html", "name": "Plasma\\Schemas\\Repository", "doc": "&quot;The repository is responsible for turning row results into specified PHP object.&quot;"},
                                                        {"type": "Method", "fromName": "Plasma\\Schemas\\Repository", "fromLink": "Plasma/Schemas/Repository.html", "link": "Plasma/Schemas/Repository.html#method___construct", "name": "Plasma\\Schemas\\Repository::__construct", "doc": "&quot;Constructor.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Repository", "fromLink": "Plasma/Schemas/Repository.html", "link": "Plasma/Schemas/Repository.html#method_getClient", "name": "Plasma\\Schemas\\Repository::getClient", "doc": "&quot;Get the internally used client.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Repository", "fromLink": "Plasma/Schemas/Repository.html", "link": "Plasma/Schemas/Repository.html#method_getSchemaBuilder", "name": "Plasma\\Schemas\\Repository::getSchemaBuilder", "doc": "&quot;Get the Schema Builder for the schema.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Repository", "fromLink": "Plasma/Schemas/Repository.html", "link": "Plasma/Schemas/Repository.html#method_registerSchemaBuilder", "name": "Plasma\\Schemas\\Repository::registerSchemaBuilder", "doc": "&quot;Register a Schema Builder for the schema to be used by the Repository.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Repository", "fromLink": "Plasma/Schemas/Repository.html", "link": "Plasma/Schemas/Repository.html#method_unregisterSchemaBuilder", "name": "Plasma\\Schemas\\Repository::unregisterSchemaBuilder", "doc": "&quot;Unregister the Schema Builder of the schema.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Repository", "fromLink": "Plasma/Schemas/Repository.html", "link": "Plasma/Schemas/Repository.html#method_getConnectionCount", "name": "Plasma\\Schemas\\Repository::getConnectionCount", "doc": "&quot;Get the amount of connections.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Repository", "fromLink": "Plasma/Schemas/Repository.html", "link": "Plasma/Schemas/Repository.html#method_checkinConnection", "name": "Plasma\\Schemas\\Repository::checkinConnection", "doc": "&quot;Checks a connection back in, if usable and not closing.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Repository", "fromLink": "Plasma/Schemas/Repository.html", "link": "Plasma/Schemas/Repository.html#method_beginTransaction", "name": "Plasma\\Schemas\\Repository::beginTransaction", "doc": "&quot;Begins a transaction. Resolves with a &lt;code&gt;TransactionInterface&lt;\/code&gt; instance.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Repository", "fromLink": "Plasma/Schemas/Repository.html", "link": "Plasma/Schemas/Repository.html#method_close", "name": "Plasma\\Schemas\\Repository::close", "doc": "&quot;Closes all connections gracefully after processing all outstanding requests.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Repository", "fromLink": "Plasma/Schemas/Repository.html", "link": "Plasma/Schemas/Repository.html#method_quit", "name": "Plasma\\Schemas\\Repository::quit", "doc": "&quot;Forcefully closes the connection, without waiting for any outstanding requests. This will reject all outstanding requests.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Repository", "fromLink": "Plasma/Schemas/Repository.html", "link": "Plasma/Schemas/Repository.html#method_runCommand", "name": "Plasma\\Schemas\\Repository::runCommand", "doc": "&quot;Runs the given command.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Repository", "fromLink": "Plasma/Schemas/Repository.html", "link": "Plasma/Schemas/Repository.html#method_runQuery", "name": "Plasma\\Schemas\\Repository::runQuery", "doc": "&quot;Runs the given querybuilder on an underlying driver instance.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Repository", "fromLink": "Plasma/Schemas/Repository.html", "link": "Plasma/Schemas/Repository.html#method_createReadCursor", "name": "Plasma\\Schemas\\Repository::createReadCursor", "doc": "&quot;Creates a new cursor to seek through SELECT query results. Resolves with a &lt;code&gt;CursorInterface&lt;\/code&gt; instance.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Repository", "fromLink": "Plasma/Schemas/Repository.html", "link": "Plasma/Schemas/Repository.html#method_query", "name": "Plasma\\Schemas\\Repository::query", "doc": "&quot;Executes a plain query. Resolves with a &lt;code&gt;QueryResultInterface&lt;\/code&gt; instance.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Repository", "fromLink": "Plasma/Schemas/Repository.html", "link": "Plasma/Schemas/Repository.html#method_prepare", "name": "Plasma\\Schemas\\Repository::prepare", "doc": "&quot;Prepares a query. Resolves with a &lt;code&gt;StatementInterface&lt;\/code&gt; instance.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Repository", "fromLink": "Plasma/Schemas/Repository.html", "link": "Plasma/Schemas/Repository.html#method_execute", "name": "Plasma\\Schemas\\Repository::execute", "doc": "&quot;Prepares and executes a query. Resolves with a &lt;code&gt;QueryResultInterface&lt;\/code&gt; instance.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Repository", "fromLink": "Plasma/Schemas/Repository.html", "link": "Plasma/Schemas/Repository.html#method_quote", "name": "Plasma\\Schemas\\Repository::quote", "doc": "&quot;Quotes the string for use in the query.&quot;"},
            {"type": "Class", "fromName": "Plasma\\Schemas", "fromLink": "Plasma/Schemas.html", "link": "Plasma/Schemas/SQLSchemaBuilder.html", "name": "Plasma\\Schemas\\SQLSchemaBuilder", "doc": "&quot;This is a SQL Schema Builder implementation.&quot;"},
                                                        {"type": "Method", "fromName": "Plasma\\Schemas\\SQLSchemaBuilder", "fromLink": "Plasma/Schemas/SQLSchemaBuilder.html", "link": "Plasma/Schemas/SQLSchemaBuilder.html#method___construct", "name": "Plasma\\Schemas\\SQLSchemaBuilder::__construct", "doc": "&quot;Constructor.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SQLSchemaBuilder", "fromLink": "Plasma/Schemas/SQLSchemaBuilder.html", "link": "Plasma/Schemas/SQLSchemaBuilder.html#method_getRepository", "name": "Plasma\\Schemas\\SQLSchemaBuilder::getRepository", "doc": "&quot;Gets the repository.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SQLSchemaBuilder", "fromLink": "Plasma/Schemas/SQLSchemaBuilder.html", "link": "Plasma/Schemas/SQLSchemaBuilder.html#method_setRepository", "name": "Plasma\\Schemas\\SQLSchemaBuilder::setRepository", "doc": "&quot;Sets the repository to use.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SQLSchemaBuilder", "fromLink": "Plasma/Schemas/SQLSchemaBuilder.html", "link": "Plasma/Schemas/SQLSchemaBuilder.html#method_fetch", "name": "Plasma\\Schemas\\SQLSchemaBuilder::fetch", "doc": "&quot;Fetch a row by the unique identifier. Resolves with an instance of &lt;code&gt;SchemaCollection&lt;\/code&gt;.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SQLSchemaBuilder", "fromLink": "Plasma/Schemas/SQLSchemaBuilder.html", "link": "Plasma/Schemas/SQLSchemaBuilder.html#method_fetchBy", "name": "Plasma\\Schemas\\SQLSchemaBuilder::fetchBy", "doc": "&quot;Fetch a row by the specified column. Resolves with an instance of &lt;code&gt;SchemaCollection&lt;\/code&gt;.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SQLSchemaBuilder", "fromLink": "Plasma/Schemas/SQLSchemaBuilder.html", "link": "Plasma/Schemas/SQLSchemaBuilder.html#method_fetchAll", "name": "Plasma\\Schemas\\SQLSchemaBuilder::fetchAll", "doc": "&quot;Fetches all rows. Resolves with an instance of &lt;code&gt;SchemaCollection&lt;\/code&gt;.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SQLSchemaBuilder", "fromLink": "Plasma/Schemas/SQLSchemaBuilder.html", "link": "Plasma/Schemas/SQLSchemaBuilder.html#method_insert", "name": "Plasma\\Schemas\\SQLSchemaBuilder::insert", "doc": "&quot;Inserts a row. Resolves with an instance of &lt;code&gt;SchemaCollection&lt;\/code&gt;, if there is a primary column. Otherwise resolves with the query result.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SQLSchemaBuilder", "fromLink": "Plasma/Schemas/SQLSchemaBuilder.html", "link": "Plasma/Schemas/SQLSchemaBuilder.html#method_insertAll", "name": "Plasma\\Schemas\\SQLSchemaBuilder::insertAll", "doc": "&quot;Inserts a list of rows. Resolves with an instance of &lt;code&gt;SchemaCollection&lt;\/code&gt;.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SQLSchemaBuilder", "fromLink": "Plasma/Schemas/SQLSchemaBuilder.html", "link": "Plasma/Schemas/SQLSchemaBuilder.html#method_update", "name": "Plasma\\Schemas\\SQLSchemaBuilder::update", "doc": "&quot;Updates the row with the given data, identified by a specific field.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SQLSchemaBuilder", "fromLink": "Plasma/Schemas/SQLSchemaBuilder.html", "link": "Plasma/Schemas/SQLSchemaBuilder.html#method_buildSchemas", "name": "Plasma\\Schemas\\SQLSchemaBuilder::buildSchemas", "doc": "&quot;Builds schemas for the given SELECT query result.&quot;"},
            {"type": "Class", "fromName": "Plasma\\Schemas", "fromLink": "Plasma/Schemas.html", "link": "Plasma/Schemas/Schema.html", "name": "Plasma\\Schemas\\Schema", "doc": "&quot;This is a schema class which maps each rows column to a camelcase property.&quot;"},
                                                        {"type": "Method", "fromName": "Plasma\\Schemas\\Schema", "fromLink": "Plasma/Schemas/Schema.html", "link": "Plasma/Schemas/Schema.html#method___construct", "name": "Plasma\\Schemas\\Schema::__construct", "doc": "&quot;Constructor.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Schema", "fromLink": "Plasma/Schemas/Schema.html", "link": "Plasma/Schemas/Schema.html#method_validateData", "name": "Plasma\\Schemas\\Schema::validateData", "doc": "&quot;Child classes can override this method to implement some sort of validation.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Schema", "fromLink": "Plasma/Schemas/Schema.html", "link": "Plasma/Schemas/Schema.html#method_build", "name": "Plasma\\Schemas\\Schema::build", "doc": "&quot;Builds a schema instance.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Schema", "fromLink": "Plasma/Schemas/Schema.html", "link": "Plasma/Schemas/Schema.html#method_getDefinition", "name": "Plasma\\Schemas\\Schema::getDefinition", "doc": "&quot;Returns the schema definition.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Schema", "fromLink": "Plasma/Schemas/Schema.html", "link": "Plasma/Schemas/Schema.html#method_getTableName", "name": "Plasma\\Schemas\\Schema::getTableName", "doc": "&quot;Returns the name of the table.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Schema", "fromLink": "Plasma/Schemas/Schema.html", "link": "Plasma/Schemas/Schema.html#method_getIdentifierColumn", "name": "Plasma\\Schemas\\Schema::getIdentifierColumn", "doc": "&quot;Returns the name of the identifier column (primary or unique), or null.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Schema", "fromLink": "Plasma/Schemas/Schema.html", "link": "Plasma/Schemas/Schema.html#method_insert", "name": "Plasma\\Schemas\\Schema::insert", "doc": "&quot;Inserts the schema.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Schema", "fromLink": "Plasma/Schemas/Schema.html", "link": "Plasma/Schemas/Schema.html#method_update", "name": "Plasma\\Schemas\\Schema::update", "doc": "&quot;Updates the row with the new data. Resolves with a &lt;code&gt;QueryResultInterface&lt;\/code&gt; instance.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Schema", "fromLink": "Plasma/Schemas/Schema.html", "link": "Plasma/Schemas/Schema.html#method_delete", "name": "Plasma\\Schemas\\Schema::delete", "doc": "&quot;Deletes the row. Resolves with a &lt;code&gt;QueryResultInterface&lt;\/code&gt; instance.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Schema", "fromLink": "Plasma/Schemas/Schema.html", "link": "Plasma/Schemas/Schema.html#method_toArray", "name": "Plasma\\Schemas\\Schema::toArray", "doc": "&quot;Returns an array with all values mapped by the column name.&quot;"},
            {"type": "Class", "fromName": "Plasma\\Schemas", "fromLink": "Plasma/Schemas.html", "link": "Plasma/Schemas/SchemaCollection.html", "name": "Plasma\\Schemas\\SchemaCollection", "doc": "&quot;Schema Collections hold schemas and the associated query result together.&quot;"},
                                                        {"type": "Method", "fromName": "Plasma\\Schemas\\SchemaCollection", "fromLink": "Plasma/Schemas/SchemaCollection.html", "link": "Plasma/Schemas/SchemaCollection.html#method___construct", "name": "Plasma\\Schemas\\SchemaCollection::__construct", "doc": "&quot;Constructor.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SchemaCollection", "fromLink": "Plasma/Schemas/SchemaCollection.html", "link": "Plasma/Schemas/SchemaCollection.html#method_getSchemas", "name": "Plasma\\Schemas\\SchemaCollection::getSchemas", "doc": "&quot;Get the stored schemas.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\SchemaCollection", "fromLink": "Plasma/Schemas/SchemaCollection.html", "link": "Plasma/Schemas/SchemaCollection.html#method_getResult", "name": "Plasma\\Schemas\\SchemaCollection::getResult", "doc": "&quot;Get the query result.&quot;"},
            {"type": "Class", "fromName": "Plasma\\Schemas", "fromLink": "Plasma/Schemas.html", "link": "Plasma/Schemas/Statement.html", "name": "Plasma\\Schemas\\Statement", "doc": "&quot;Represents a Prepared Statement. This class however wraps a statement instance.&quot;"},
                                                        {"type": "Method", "fromName": "Plasma\\Schemas\\Statement", "fromLink": "Plasma/Schemas/Statement.html", "link": "Plasma/Schemas/Statement.html#method___construct", "name": "Plasma\\Schemas\\Statement::__construct", "doc": "&quot;Constructor.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Statement", "fromLink": "Plasma/Schemas/Statement.html", "link": "Plasma/Schemas/Statement.html#method_getID", "name": "Plasma\\Schemas\\Statement::getID", "doc": "&quot;Get the driver-dependent ID of this statement.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Statement", "fromLink": "Plasma/Schemas/Statement.html", "link": "Plasma/Schemas/Statement.html#method_getQuery", "name": "Plasma\\Schemas\\Statement::getQuery", "doc": "&quot;Get the prepared query.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Statement", "fromLink": "Plasma/Schemas/Statement.html", "link": "Plasma/Schemas/Statement.html#method_isClosed", "name": "Plasma\\Schemas\\Statement::isClosed", "doc": "&quot;Whether the statement has been closed.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Statement", "fromLink": "Plasma/Schemas/Statement.html", "link": "Plasma/Schemas/Statement.html#method_close", "name": "Plasma\\Schemas\\Statement::close", "doc": "&quot;Closes the prepared statement and frees the associated resources on the server.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Statement", "fromLink": "Plasma/Schemas/Statement.html", "link": "Plasma/Schemas/Statement.html#method_execute", "name": "Plasma\\Schemas\\Statement::execute", "doc": "&quot;Executes the prepared statement. Resolves with a &lt;code&gt;QueryResult&lt;\/code&gt; instance.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Statement", "fromLink": "Plasma/Schemas/Statement.html", "link": "Plasma/Schemas/Statement.html#method_runQuery", "name": "Plasma\\Schemas\\Statement::runQuery", "doc": "&quot;Runs the given querybuilder on an underlying driver instance.&quot;"},
            {"type": "Class", "fromName": "Plasma\\Schemas", "fromLink": "Plasma/Schemas.html", "link": "Plasma/Schemas/Transaction.html", "name": "Plasma\\Schemas\\Transaction", "doc": "&quot;Represents a Transaction. This class wraps a Transaction.&quot;"},
                                                        {"type": "Method", "fromName": "Plasma\\Schemas\\Transaction", "fromLink": "Plasma/Schemas/Transaction.html", "link": "Plasma/Schemas/Transaction.html#method___construct", "name": "Plasma\\Schemas\\Transaction::__construct", "doc": "&quot;Constructor.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Transaction", "fromLink": "Plasma/Schemas/Transaction.html", "link": "Plasma/Schemas/Transaction.html#method___destruct", "name": "Plasma\\Schemas\\Transaction::__destruct", "doc": "&quot;Destructor.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Transaction", "fromLink": "Plasma/Schemas/Transaction.html", "link": "Plasma/Schemas/Transaction.html#method_getIsolationLevel", "name": "Plasma\\Schemas\\Transaction::getIsolationLevel", "doc": "&quot;Get the isolation level for this transaction.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Transaction", "fromLink": "Plasma/Schemas/Transaction.html", "link": "Plasma/Schemas/Transaction.html#method_isActive", "name": "Plasma\\Schemas\\Transaction::isActive", "doc": "&quot;Whether the transaction is still active, or has been committed\/rolled back.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Transaction", "fromLink": "Plasma/Schemas/Transaction.html", "link": "Plasma/Schemas/Transaction.html#method_query", "name": "Plasma\\Schemas\\Transaction::query", "doc": "&quot;Executes a plain query. Resolves with a &lt;code&gt;QueryResult&lt;\/code&gt; instance.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Transaction", "fromLink": "Plasma/Schemas/Transaction.html", "link": "Plasma/Schemas/Transaction.html#method_prepare", "name": "Plasma\\Schemas\\Transaction::prepare", "doc": "&quot;Prepares a query. Resolves with a &lt;code&gt;StatementInterface&lt;\/code&gt; instance.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Transaction", "fromLink": "Plasma/Schemas/Transaction.html", "link": "Plasma/Schemas/Transaction.html#method_execute", "name": "Plasma\\Schemas\\Transaction::execute", "doc": "&quot;Prepares and executes a query. Resolves with a &lt;code&gt;QueryResultInterface&lt;\/code&gt; instance.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Transaction", "fromLink": "Plasma/Schemas/Transaction.html", "link": "Plasma/Schemas/Transaction.html#method_quote", "name": "Plasma\\Schemas\\Transaction::quote", "doc": "&quot;Quotes the string for use in the query.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Transaction", "fromLink": "Plasma/Schemas/Transaction.html", "link": "Plasma/Schemas/Transaction.html#method_runQuery", "name": "Plasma\\Schemas\\Transaction::runQuery", "doc": "&quot;Runs the given querybuilder on the underlying driver instance.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Transaction", "fromLink": "Plasma/Schemas/Transaction.html", "link": "Plasma/Schemas/Transaction.html#method_commit", "name": "Plasma\\Schemas\\Transaction::commit", "doc": "&quot;Commits the changes.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Transaction", "fromLink": "Plasma/Schemas/Transaction.html", "link": "Plasma/Schemas/Transaction.html#method_rollback", "name": "Plasma\\Schemas\\Transaction::rollback", "doc": "&quot;Rolls back the changes.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Transaction", "fromLink": "Plasma/Schemas/Transaction.html", "link": "Plasma/Schemas/Transaction.html#method_createSavepoint", "name": "Plasma\\Schemas\\Transaction::createSavepoint", "doc": "&quot;Creates a savepoint with the given identifier.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Transaction", "fromLink": "Plasma/Schemas/Transaction.html", "link": "Plasma/Schemas/Transaction.html#method_rollbackTo", "name": "Plasma\\Schemas\\Transaction::rollbackTo", "doc": "&quot;Rolls back to the savepoint with the given identifier.&quot;"},
                    {"type": "Method", "fromName": "Plasma\\Schemas\\Transaction", "fromLink": "Plasma/Schemas/Transaction.html", "link": "Plasma/Schemas/Transaction.html#method_releaseSavepoint", "name": "Plasma\\Schemas\\Transaction::releaseSavepoint", "doc": "&quot;Releases the savepoint with the given identifier.&quot;"},
            
                                        // Fix trailing commas in the index
        {}
    ];

    /** Tokenizes strings by namespaces and functions */
    function tokenizer(term) {
        if (!term) {
            return [];
        }

        var tokens = [term];
        var meth = term.indexOf('::');

        // Split tokens into methods if "::" is found.
        if (meth > -1) {
            tokens.push(term.substr(meth + 2));
            term = term.substr(0, meth - 2);
        }

        // Split by namespace or fake namespace.
        if (term.indexOf('\\') > -1) {
            tokens = tokens.concat(term.split('\\'));
        } else if (term.indexOf('_') > 0) {
            tokens = tokens.concat(term.split('_'));
        }

        // Merge in splitting the string by case and return
        tokens = tokens.concat(term.match(/(([A-Z]?[^A-Z]*)|([a-z]?[^a-z]*))/g).slice(0,-1));

        return tokens;
    };

    root.Sami = {
        /**
         * Cleans the provided term. If no term is provided, then one is
         * grabbed from the query string "search" parameter.
         */
        cleanSearchTerm: function(term) {
            // Grab from the query string
            if (typeof term === 'undefined') {
                var name = 'search';
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
                var results = regex.exec(location.search);
                if (results === null) {
                    return null;
                }
                term = decodeURIComponent(results[1].replace(/\+/g, " "));
            }

            return term.replace(/<(?:.|\n)*?>/gm, '');
        },

        /** Searches through the index for a given term */
        search: function(term) {
            // Create a new search index if needed
            if (!bhIndex) {
                bhIndex = new Bloodhound({
                    limit: 500,
                    local: searchIndex,
                    datumTokenizer: function (d) {
                        return tokenizer(d.name);
                    },
                    queryTokenizer: Bloodhound.tokenizers.whitespace
                });
                bhIndex.initialize();
            }

            results = [];
            bhIndex.get(term, function(matches) {
                results = matches;
            });

            if (!rootPath) {
                return results;
            }

            // Fix the element links based on the current page depth.
            return $.map(results, function(ele) {
                if (ele.link.indexOf('..') > -1) {
                    return ele;
                }
                ele.link = rootPath + ele.link;
                if (ele.fromLink) {
                    ele.fromLink = rootPath + ele.fromLink;
                }
                return ele;
            });
        },

        /** Get a search class for a specific type */
        getSearchClass: function(type) {
            return searchTypeClasses[type] || searchTypeClasses['_'];
        },

        /** Add the left-nav tree to the site */
        injectApiTree: function(ele) {
            ele.html(treeHtml);
        }
    };

    $(function() {
        // Modify the HTML to work correctly based on the current depth
        rootPath = $('body').attr('data-root-path');
        treeHtml = treeHtml.replace(/href="/g, 'href="' + rootPath);
        Sami.injectApiTree($('#api-tree'));
    });

    return root.Sami;
})(window);

$(function() {

    // Enable the version switcher
    $('#version-switcher').change(function() {
        window.location = $(this).val()
    });

    
        // Toggle left-nav divs on click
        $('#api-tree .hd span').click(function() {
            $(this).parent().parent().toggleClass('opened');
        });

        // Expand the parent namespaces of the current page.
        var expected = $('body').attr('data-name');

        if (expected) {
            // Open the currently selected node and its parents.
            var container = $('#api-tree');
            var node = $('#api-tree li[data-name="' + expected + '"]');
            // Node might not be found when simulating namespaces
            if (node.length > 0) {
                node.addClass('active').addClass('opened');
                node.parents('li').addClass('opened');
                var scrollPos = node.offset().top - container.offset().top + container.scrollTop();
                // Position the item nearer to the top of the screen.
                scrollPos -= 200;
                container.scrollTop(scrollPos);
            }
        }

    
    
        var form = $('#search-form .typeahead');
        form.typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            name: 'search',
            displayKey: 'name',
            source: function (q, cb) {
                cb(Sami.search(q));
            }
        });

        // The selection is direct-linked when the user selects a suggestion.
        form.on('typeahead:selected', function(e, suggestion) {
            window.location = suggestion.link;
        });

        // The form is submitted when the user hits enter.
        form.keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').submit();
                return true;
            }
        });

    
});


