// Don't use this!!!
package ideas;

import javax.jdo.JDOHelper;
import javax.jdo.PersistenceManager;
import javax.jdo.PersistenceManagerFactory;
import javax.jdo.Transaction;
import com.google.appengine.api.datastore.Text ;
import java.util.List;
import java.util.ArrayList;
import ideas.JsonPDO;
import ideas.PMF;

public class Storage {
    public List<String> getLogins(){
        PersistenceManager pm = PMF.get().getPersistenceManager();
        pm.setDetachAllOnCommit(true);
        List<String> logins = new ArrayList<String>();
        List<JsonPDO> objs = null ;
        try {
            String query = "select from " + JsonPDO.class.getName() + " ";
            objs = (List<JsonPDO>) pm.newQuery(query).execute();
        } finally {
           //pm.close();
        }
        if(objs != null) {
            if(objs.size() >= 1) {
                for(JsonPDO user: objs) {
                    if(user != null && user.getLogin() != null) {
                        logins.add(user.getLogin());
                    }
                }
            }
        }
        return logins;
    }
    public JsonPDO get(String login ){
        PersistenceManager pm = PMF.get().getPersistenceManager();
        pm.setDetachAllOnCommit(true);
        List<JsonPDO> objs = null ;
        try {
            String query = "select from " + JsonPDO.class.getName() + " where login == '"+login+"'";
            objs = (List<JsonPDO>) pm.newQuery(query).execute();
        } finally {
           //pm.close();
        }
        if(objs != null) {
            if(objs.size() >= 1) {
                return objs.get(0) ;
            }
        }
        return null;
    }

    public void put(String login, String json) {
        PersistenceManager pm = PMF.get().getPersistenceManager();
        pm.setDetachAllOnCommit(true);
        Transaction tx = pm.currentTransaction();

        List<JsonPDO> objs = null ;
        try {
            String query = "select from " + JsonPDO.class.getName() + " where login == '"+login+"'";
            objs = (List<JsonPDO>) pm.newQuery(query).execute();
        } finally {
        }

        JsonPDO obj = null ;

        tx.begin();
        if(objs != null) {
            if(objs.size() >= 1) {
                obj = pm.getObjectById(JsonPDO.class,objs.get(0).getId()) ;
            }
        }

        if(obj == null) {
            obj = new JsonPDO();
            obj.setLogin(login);
            obj.setNewJson(new Text(json));
        } else{
            obj.setNewJson(new Text(json));
        }
        try {
            pm.makePersistent(obj);
            tx.commit();
        } finally {
            //pm.close();
        }
    }

    public void close() {
        PersistenceManager pm = PMF.get().getPersistenceManager();
        pm.setDetachAllOnCommit(true);
        if(!pm.isClosed())
            pm.close();
    }
}
