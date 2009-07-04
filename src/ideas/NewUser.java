package ideas;

import javax.jdo.annotations.IdGeneratorStrategy;
import javax.jdo.annotations.IdentityType;
import javax.jdo.annotations.PersistenceCapable;
import javax.jdo.annotations.Persistent;
import javax.jdo.annotations.Transactional;
import javax.jdo.annotations.PrimaryKey;
import com.google.appengine.api.datastore.Text ;
import java.util.List;
import java.util.ArrayList;

@PersistenceCapable(identityType = IdentityType.APPLICATION,detachable="true")
public class NewUser {

    //@PrimaryKey
    //@Persistent(valueStrategy = IdGeneratorStrategy.IDENTITY)
    //public Long key;
    
    @Persistent
    public String password ;
    
    @PrimaryKey
    @Persistent
    public String login ;

    @Transactional
    public List<NewIdea> ideas = new ArrayList<NewIdea>();

}
