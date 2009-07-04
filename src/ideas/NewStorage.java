package ideas;

import javax.jdo.JDOHelper;
import javax.jdo.PersistenceManager;
import javax.jdo.PersistenceManagerFactory;
import javax.jdo.Transaction;
import com.google.appengine.api.datastore.Text ;
import java.util.List;
import java.util.ArrayList;

public class NewStorage {

    public NewUser get(String login) {
        return getUser(login);
    }

    public NewUser getUser(String login) {
        PersistenceManager pm = PMF.get().getPersistenceManager();
        pm.setDetachAllOnCommit(true);
        NewUser user ;
        try {
            user = (NewUser) pm.getObjectById(NewUser.class,login);
        } catch (Exception e) {
            user = new NewUser();
        } finally {
           close();
        }
        user.ideas = getIdeas(user.login);
        return user;
    }

    public NewIdea getIdea(String id){
        PersistenceManager pm = PMF.get().getPersistenceManager();
        pm.setDetachAllOnCommit(true);
        NewIdea idea ;
        try {
            //idea = pm.getObjectById(NewIdea.class,id);
            String query = "select from "+ NewIdea.class.getName() +" where _id == '"+id+"'";
            idea = ((List<NewIdea>) pm.newQuery(query).execute()).get(0);
            System.out.println("GET idea "+id+" found ["+idea.login+"]!");
        } catch(Exception e) {
            idea = new NewIdea();
            System.out.println("GET idea "+id+" not found!");
        } finally {
           close();
        }
        return idea; 
    }

    public List<NewIdea> getIdeas(String login){
        PersistenceManager pm = PMF.get().getPersistenceManager();
        pm.setDetachAllOnCommit(true);
        List<NewIdea> objs = null ;
        try {
            String query = "select from " + NewIdea.class.getName() + " where login == '"+login+"'";
            objs = (List<NewIdea>) pm.newQuery(query).execute();
            if(objs.size() >= 1) {
                for(NewIdea idea: objs) {
                    System.out.println("Select ["+idea.login+"] "+idea._id+" -> "+idea.idea.getValue());
                }
            }
        } finally {
           close();
        }
        return objs; 
    }

    public void deleteAll() {
        deleteUsers();
        deleteIdeas();
    }

    private void deleteUsers() {
        PersistenceManager pm = PMF.get().getPersistenceManager();
        Transaction tx= pm.currentTransaction();
        String query = "select from " + NewUser.class.getName() + " ";
        List<NewUser> users = (List<NewUser>) pm.newQuery(query).execute();
        if(users != null && users.size() >= 1 ) {
            for(NewUser user: users) {
                System.out.println("Will delete "+user.login);
                pm.deletePersistent(user);
            }
        }
    }

    public void deleteUser(String login) {
        PersistenceManager pm = PMF.get().getPersistenceManager();
        Transaction tx= pm.currentTransaction();
        NewUser user = (NewUser) pm.getObjectById(NewUser.class,login);
        pm.deletePersistent(user);
    }

    private void deleteIdeas() {
        PersistenceManager pm = PMF.get().getPersistenceManager();
        Transaction tx= pm.currentTransaction();
        String query = "select from " + NewIdea.class.getName() + " ";
        List<NewIdea> ideas = (List<NewIdea>) pm.newQuery(query).execute();
        if(ideas != null && ideas.size() >=1) {
            for(NewIdea idea: ideas) {
                pm.deletePersistent(idea);
            }
        }

    }

    public void put(String login, NewUser myUser) {
        putUser(login,myUser);
        putIdeas(login,myUser.ideas);
    }

    public void putUser(String login, NewUser myUser) {
        PersistenceManager pm = PMF.get().getPersistenceManager();
        Transaction tx= pm.currentTransaction();
        tx.begin();
        NewUser user;
        try {
            user = (NewUser) pm.getObjectById(NewUser.class,login) ;
        } catch(Exception e){
            user = new NewUser();
        }
        user.login = myUser.login;
        user.password = myUser.password;
        pm.makePersistent(user);
        tx.commit();
    }


    public void putIdeas(String login,List<NewIdea> newIdeas) {
        PersistenceManager pm = PMF.get().getPersistenceManager();
        pm.setDetachAllOnCommit(true);
        Transaction tx= pm.currentTransaction();
        for(NewIdea idea: newIdeas) {
            tx.begin();
            idea._id = "_"+idea.id;
            idea.login = login;
            pm.makePersistent(idea);
            tx.commit();
            System.out.println("Saved ["+idea.login+"] "+idea._id+" -> "+idea.idea.getValue());
        }
        close();
    }

    public void putIdea(String login,NewIdea idea) {
        PersistenceManager pm = PMF.get().getPersistenceManager();
        pm.setDetachAllOnCommit(true);
        Transaction tx= pm.currentTransaction();
        tx.begin();
        idea._id = "_"+idea.id;
        idea.login = login;
        pm.makePersistent(idea);
        tx.commit();
        System.out.println("Saved ["+idea.login+"] "+idea._id+" -> "+idea.idea.getValue());
        close();
    }

    public void close() {
        PersistenceManager pm = PMF.get().getPersistenceManager();
        if(!pm.isClosed()) {
            System.out.println("Closed DB!");
            pm.close();
        }
    }
}
