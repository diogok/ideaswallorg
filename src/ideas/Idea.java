// Don't use this!!!
package ideas;

import javax.jdo.annotations.IdGeneratorStrategy;
import javax.jdo.annotations.IdentityType;
import javax.jdo.annotations.PersistenceCapable;
import javax.jdo.annotations.Persistent;
import javax.jdo.annotations.PrimaryKey;
import com.google.appengine.api.datastore.Text ;
import java.util.List;
import java.util.ArrayList;


@PersistenceCapable(identityType = IdentityType.APPLICATION,detachable="true")
public class Idea {

    //@PrimaryKey
    //@Persistent(valueStrategy = IdGeneratorStrategy.IDENTITY)
    //public Long key;

    @PrimaryKey
    @Persistent
    public String id ;

    @Persistent
    public Text idea;

    @Persistent
    public String login;

    @Persistent
    public int status ;
    
    @Persistent
    public int priori ;

    @Persistent
    public List<String> tags = new ArrayList<String>();

    @Persistent
    public int x ;

    @Persistent
    public int y;

    @Persistent
    public int date ;


    public void setText(String text) {
        idea = new Text(text);
    }

}
